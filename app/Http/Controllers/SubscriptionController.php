<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Transaction as PaymentTransaction;


class SubscriptionController extends Controller
{

    public function showUpgradePage()
    {
        return view('subscription.upgrade');
    }

    public function renew()
    {
        return view('subscription.renew');
    }

    public function show($orderId)
    {
        // // Dummy data, nanti bisa kamu ganti ambil dari database kalau ada
        // $order = [
        //     'id' => $orderId,
        //     'status' => (str_starts_with($orderId, 'RENEW-')) ? 'Lunas' : 'Belum Dibayar',
        //     'amount' => (str_starts_with($orderId, 'RENEW-')) ? 2000000 : 5000000,
        // ];

        // return view('payment.details', compact('order'));
        return view('subscription.upgrade');
    }

    public function getSnapToken(Request $request)
    {
        try {
            $type = strtolower($request->query('type')); // 'upgrade' atau 'renew'
            $plan = strtolower($request->query('plan'));

            $price = 0;
            $order_id = '';

            if ($type === 'upgrade') {
                \Log::info('UPGRADE PLAN:', [$plan]);

                // Validasi plan yang sesuai dengan blade
                if (!in_array($plan, ['standard', 'pro'])) {
                    return response()->json(['error' => 'Invalid plan type'], 400);
                }

                $price = match($plan) {
                    'standard' => 2000000,
                    'pro' => 5000000,
                };

                $order_id = 'UPGRADE-' . strtoupper($plan) . '-' . uniqid();

            } elseif ($type === 'renew') {
                $user = auth()->user();
                \Log::info('RENEW PLAN FOR:', [$user->email]);

                // Validasi profile user
                if (!in_array(strtolower($user->profile), ['standard', 'pro'])) {
                    return response()->json(['error' => 'Invalid user profile for renewal'], 400);
                }

                $price = match(strtolower($user->profile)) {
                    'standard' => 2000000,
                    'pro' => 5000000,
                };

                $order_id = 'RENEW-' . strtoupper($user->profile) . '-' . uniqid();
            } else {
                return response()->json(['error' => 'Invalid request type. Use "upgrade" or "renew"'], 400);
            }

            // Midtrans config
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');
            \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
            \Midtrans\Config::$is3ds = config('midtrans.is_3ds');

            // Setup transaction details
            $params = [
                'transaction_details' => [
                    'order_id' => $order_id,
                    'gross_amount' => $price,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->company_name ?? auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $plan,
                        'price' => $price,
                        'quantity' => 1,
                        'name' => ucfirst($plan) . ' Plan Subscription',
                    ]
                ]
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            return response()->json([
                'token' => $snapToken,
                'order_id' => $order_id // Tambahkan order_id di response
            ]);

        } catch (\Throwable $e) {
            \Log::error('MIDTRANS TOKEN ERROR: ' . $e->getMessage());
            return response()->json(['error' => 'Payment gateway error. Please try again.'], 500);
        }
    }


    public function paymentSuccess(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $orderId = $request->query('order_id');
        if (!$orderId) {
            return redirect()->route('dashboard')->with('error', 'Order ID tidak ditemukan.');
        }

        // Debug order_id yang diterima
        \Log::info('Payment success with order_id: ' . $orderId);

        $startDate = now();
        $endDate = $startDate->copy()->addYear();

        // Verifikasi transaksi dengan Midtrans
        try {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = config('midtrans.is_production');

            $midtransResponse = \Midtrans\Transaction::status($orderId);

            if (!in_array($midtransResponse->transaction_status, ['settlement', 'capture'])) {
                \Log::warning('Payment not completed for order_id: ' . $orderId);
                return redirect()->route('dashboard')->with('error', 'Pembayaran belum berhasil diproses.');
            }

            $amount = (int) $midtransResponse->gross_amount;
        } catch (\Exception $e) {
            \Log::error('Midtrans verification failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal memverifikasi pembayaran.');
        }

        // Proses update langganan
        try {
            if (Str::startsWith($orderId, 'RENEW-')) {
                $user->subscribed_until = optional($user->subscribed_until)->isFuture()
                    ? $user->subscribed_until->addYear()
                    : $endDate;

                // Tidak perlu update profile untuk renewal
            } elseif (Str::contains($orderId, 'UPGRADE-STANDARD-')) {
                $user->subscribed_until = $endDate;
                $user->profile = 'Standard';
            } elseif (Str::contains($orderId, 'UPGRADE-PRO-')) {
                $user->subscribed_until = $endDate;
                $user->profile = 'Pro';
            } else {
                \Log::warning('Unknown order_id format: ' . $orderId);
                return redirect()->route('dashboard')->with('error', 'Format Order ID tidak valid.');
            }

            $user->is_subscribed = true;
            $user->save();

            // Simpan transaksi
            PaymentTransaction::create([
                'user_id'    => $user->id,
                'order_id'   => $orderId,
                'type'       => Str::startsWith($orderId, 'RENEW-') ? 'renewal' : 'new_subscription',
                'amount'     => $amount,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'PAID',
                'payment_method' => $midtransResponse->payment_type ?? null,
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Subscription update failed: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal memperbarui langganan: ' . $e->getMessage());
        }

        // Generate dan kirim invoice
        return $this->generateAndSendInvoice($user, $orderId, $amount);
    }

    protected function generateAndSendInvoice($user, $orderId, $amount)
    {
        try {
            $filename = 'invoice_' . $orderId . '.pdf';
            $relativePath = 'invoices/' . $filename;
            $fullPath = storage_path('app/' . $relativePath);

            // Buat folder invoices jika belum ada
            if (!File::exists(storage_path('app/invoices'))) {
                File::makeDirectory(storage_path('app/invoices'), 0777, true);
            }

            // Get transaction status
            $transaction = PaymentTransaction::where('order_id', $orderId)->first();
            $status = $transaction ? strtoupper($transaction->status) : 'PAID';

            // Generate QR Code
            $qrContent = route('payment.details', ['order' => $orderId]);
            $qrCode = QrCode::format('png')->size(150)->generate($qrContent);
            $qrCodeBase64 = base64_encode($qrCode);

            // Generate PDF
            $pdf = Pdf::loadView('invoices.pdf', [
                'orderId' => $orderId,
                'user'    => $user,
                'amount'  => $amount,
                'status'  => $status,
                'qrCodeBase64' => $qrCodeBase64,
                'plan'    => $user->profile,
                'date'    => now()->format('d F Y'),
            ])->setOptions(['isRemoteEnabled' => true]);

            $pdf->save($fullPath);

            // Simpan invoice ke database
            Invoice::create([
                'user_id'   => $user->id,
                'order_id'  => $orderId,
                'amount'    => $amount,
                'file_path' => $relativePath,
            ]);

            // Kirim email
            Mail::to($user->email)->queue(new InvoiceMail($relativePath, $orderId, $amount));

            return redirect()->route('dashboard')
                ->with('success', 'Berhasil memperbarui langganan! Invoice telah dikirim ke email Anda.')
                ->with('invoice', $invoice);

        } catch (\Exception $e) {
            \Log::error('Invoice generation failed: ' . $e->getMessage());
            return redirect()->route('dashboard')
                ->with('success', 'Berhasil memperbarui langganan!')
                ->with('warning', 'Gagal menggenerate invoice: ' . $e->getMessage());
        }
    }


    //27 mei 2025
    public function previewInvoice($orderId)
    {
        $user = auth()->user();
        $amount = 0;

        // Logika penentuan amount yang lebih akurat
        if (Str::startsWith($orderId, 'RENEW-')) {
            // Untuk perpanjangan, sesuaikan dengan profile user saat ini
            $amount = (strtolower($user->profile) === 'standard') ? 2000000 : 5000000;
        } elseif (Str::contains(strtolower($orderId), 'upgrade-standard-')) {
            // Untuk upgrade ke standard
            $amount = 2000000;
        } elseif (Str::contains(strtolower($orderId), 'upgrade-pro-')) {
            // Untuk upgrade ke pro
            $amount = 5000000;
        } else {
            // Default fallback
            $amount = 5000000;
        }

        // Ambil transaksi berdasar orderId
        $transaction = PaymentTransaction::where('order_id', $orderId)->first();

        // Gunakan amount dari database jika tersedia
        if ($transaction && $transaction->amount > 0) {
            $amount = $transaction->amount;
        }

        $status = $transaction ? strtoupper($transaction->status) : 'PENDING';

        // Generate QR code
        $qrContent = route('payment.details', ['order' => $orderId]);
        $qrCode = QrCode::format('png')->size(150)->generate($qrContent);
        $qrCodeBase64 = base64_encode($qrCode);

        $pdf = PDF::loadView('invoices.pdf', [
            'orderId' => $orderId,
            'user' => $user,
            'amount' => $amount,
            'status' => $status,
            'qrCodeBase64' => $qrCodeBase64,
        ])->setOptions(['isRemoteEnabled' => true]);

        return $pdf->stream('invoice_' . $orderId . '.pdf');
    }

    public function generateAndSaveInvoice($orderId)
    {
        $user = auth()->user();
        $amount = 0;

        if (Str::startsWith($orderId, 'RENEW-')) {
            $user->subscribed_until = optional($user->subscribed_until)->isFuture()
                ? $user->subscribed_until->addYear()
                : now()->addYear();
            $amount = ($user->profile === 'standard') ? 2000000 : 5000000;
        } else {
            $user->subscribed_until = now()->addYear();
            $user->profile = 'pro';
            $amount = 5000000;
        }

        $user->is_subscribed = true;
        $user->save();

        // Ambil status transaksi terbaru dari tabel
        $transaction = PaymentTransaction::where('order_id', $orderId)->first();
        $status = $transaction ? strtoupper($transaction->status) : 'PENDING';

        $pdf = PDF::loadView('invoices.pdf', [
            'orderId' => $orderId,
            'user' => $user,
            'amount' => $amount,
            'status' => $status,
        ])->setOptions(['isRemoteEnabled' => true]);

        $filename = 'invoice_' . $orderId . '.pdf';
        $relativePath = 'invoices/' . $filename;
        $fullPath = storage_path('app/' . $relativePath);

        if (!File::exists(dirname($fullPath))) {
            File::makeDirectory(dirname($fullPath), 0777, true);
        }

        $pdf->save($fullPath);

        return back()->with('success', 'Invoice berhasil dibuat.')
                    ->with('invoice_path', $relativePath);
    }

}
