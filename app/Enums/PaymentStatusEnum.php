<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case Paid = 'LUNAS';
    case Partial = 'LUNAS SEBAGIAN';
    case Unpaid = 'BELUM LUNAS';
    case Cancelled = 'DIBATALKAN';

    public function color(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::Partial => 'warning',
            self::Unpaid => 'gray',
            self::Cancelled => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Paid => 'heroicon-o-check-badge',
            self::Partial => 'heroicon-o-clock',
            self::Unpaid => 'heroicon-o-minus-circle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function isFinalStatus(): bool
    {
        if ($this === self::Paid || $this === self::Cancelled) {
            return true;
        }

        return false;
    }

    public function translation(): string
    {
        return match ($this) {
            self::Paid => __('payment.statuses.paid'),
            self::Partial => __('payment.statuses.partial'),
            self::Unpaid => __('payment.statuses.unpaid'),
            self::Cancelled => __('payment.statuses.cancelled'),
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            self::Paid->value => __('payment.statuses.paid'),
            self::Partial->value => __('payment.statuses.partial'),
            self::Unpaid->value => __('payment.statuses.unpaid'),
            self::Cancelled->value => __('payment.statuses.cancelled'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function icons(): array
    {
        return [
            self::Paid->value => 'heroicon-o-check-badge',
            self::Partial->value => 'heroicon-o-clock',
            self::Unpaid->value => 'heroicon-o-minus-circle',
            self::Cancelled->value => 'heroicon-o-x-circle',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return [
            self::Paid->value => 'success',
            self::Partial->value => 'warning',
            self::Unpaid->value => 'gray',
            self::Cancelled->value => 'danger',
        ];
    }
}
