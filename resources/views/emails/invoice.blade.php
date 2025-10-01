@component('mail::message')
# Terima Kasih atas Pembayaran Anda

Berikut adalah invoice untuk pembayaran Anda:

- Nomor Invoice: {{ $invoice->order_id }}
- Jumlah: Rp {{ number_format($invoice->amount, 0, ',', '.') }}

Invoice juga terlampir dalam format PDF.

@component('mail::button', ['url' => route('dashboard')])
Kembali ke Dashboard
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
