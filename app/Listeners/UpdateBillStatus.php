<?php

namespace App\Listeners;

use App\Enums\PaymentStatusEnum;
use App\Events\TransactionBillCreated;
use App\Models\TransactionBills;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateBillStatus
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
    public function handle(TransactionBillCreated $event): void
    {
        $transactionId = $event->transactionBill->transaction_id;
        $latestBill = TransactionBills::whereTransactionId($transactionId)
            ->where('id', '!=', $event->transactionBill->id)
            ->latest('created_at')
            ->first();
        if ($latestBill === null) {
            return;
        }

        $totalBill = $event->transactionBill->amount + $event->transactionBill->penalty_amount + $event->transactionBill->arrears_amount;
        $latestBill->loadSum(relations: 'payments', column: 'nominal');
        $amountPaid = (int) $latestBill->payments_sum_nominal;

        $latestBill->status = match (true) {
            $amountPaid >= $totalBill => PaymentStatusEnum::Paid->value,
            $amountPaid > 0 && $amountPaid < $totalBill => PaymentStatusEnum::Partial->value,
            $amountPaid === 0 => PaymentStatusEnum::Unpaid->value,
        };

        $latestBill->is_payable = false;
        $latestBill->saveQuietly();
    }
}
