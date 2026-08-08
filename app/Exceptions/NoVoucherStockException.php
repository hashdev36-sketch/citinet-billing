<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a payment has been verified as successful but no unused voucher
 * exists to fulfil it. This must never be silently swallowed — a customer has
 * paid and has nothing to show for it. The webhook controller catches this,
 * marks the order as 'paid' (not 'fulfilled'), and alerts an admin so the
 * order can be fulfilled manually and the customer notified.
 */
class NoVoucherStockException extends RuntimeException
{
}
