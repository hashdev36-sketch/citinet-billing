<?php

namespace App\Jobs;

use App\Mail\VoucherPurchased;
use App\Models\Order;
use App\Models\Voucher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendVoucherEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public array $backoff = [10, 30, 60, 300, 900]; // retry with increasing delay if Resend/SMTP hiccups

    public function __construct(
        public Order $order,
        public Voucher $voucher,
    ) {}

    public function handle(): void
    {
        // Email is explicitly optional per spec — the voucher is always shown on-screen
        // and always retrievable from the dashboard regardless of whether this succeeds.
        Mail::to($this->order->customer->email)->send(
            new VoucherPurchased($this->order, $this->voucher)
        );
    }
}
