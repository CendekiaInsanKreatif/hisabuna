<?php

namespace App\Models;

use App\Mail\InvoiceMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate PDF invoice, simpan di storage dan update record
     */
    public function generateInvoice($user, $order_id, $amount)
    {
        $this->user_id = $user->id;
        $this->order_id = $order_id;
        $this->amount = $amount;

        // Generate PDF dari view invoice dengan data lengkap
        $pdf = Pdf::loadView('invoice', [
            'orderId' => $order_id,
            'user' => $user,
            'amount' => $amount,
        ]);

        // Simpan PDF ke storage/app/invoices/
        $fileName = uniqid('invoice_') . '.pdf';
        $filePath = 'invoices/' . $fileName;
        Storage::put($filePath, $pdf->output());

        // Update file_path dan simpan model
        $this->file_path = $filePath;
        $this->save();

        return $this;
    }

    /**
     * Kirim email invoice dengan file PDF terlampir
     */
    public function sendInvoiceEmail($user)
    {
        Mail::to($user->email)->send(new InvoiceMail($this));
    }

    /**
     * URL untuk akses invoice (jika pakai symlink storage)
     */
    public function getInvoiceUrl()
    {
        return url('storage/' . $this->file_path);
    }

    /**
     * Nama file invoice saja
     */
    public function getInvoiceFileName()
    {
        return basename($this->file_path);
    }
}
