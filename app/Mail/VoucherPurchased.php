<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoucherPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public Voucher $voucher,
    ) {}

    public function build(): self
    {
        return $this->subject("Your {$this->voucher->package->name} voucher — {$this->order->order_number}")
            ->view('emails.voucher-purchased');
    }
}
