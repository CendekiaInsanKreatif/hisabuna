<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Terima instance Invoice model
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build email, attach PDF invoice dari storage
     */
    public function build()
    {
        return $this->markdown('emails.invoice')
            ->subject('Invoice Pembayaran Anda')
            ->attach(storage_path('app/' . $this->invoice->file_path), [
                'as' => 'invoice_' . $this->invoice->order_id . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
