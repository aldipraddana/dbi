<?php

namespace App\Listeners;

use App\Enums\PaymentStatusEnum;
use App\Events\TransactionSaved;

class UpdateAssetRentalStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionSaved $event): void
    {
        if (PaymentStatusEnum::from((string) $event->transaction->payment_status)->isFinalStatus() === true) {
            $event->transaction->assets->is_rentalable = true;
            $event->transaction->assets->saveQuietly();
        } else {
            $event->transaction->assets->is_rentalable = false;
            $event->transaction->assets->saveQuietly();
        }
    }
}
