<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'source',
        'shipping_address',
        'code',
        'payment_method',
        'payment_status',
        'shipping_cost',
        'coupon_discount',
        'handling_fee',
        'grand_total',
        'delivery_status',
        'new_order_notification',
        'note',
        'admin_order',
        'waybill_no'
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'admin_order' => 'boolean',
        'new_order_notification' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderInfo::class);
    }



    public function getPaymentProofAttribute()
    {
        return $this->getFirstMediaUrl('payment_proof');
    }

    /**
     * Get payment reference from note field
     */
    public function getPaymentReferenceAttribute()
    {
        if ($this->note && str_starts_with($this->note, 'Payment Reference: ')) {
            return str_replace('Payment Reference: ', '', $this->note);
        }
        return null;
    }

    /**
     * Check if order is prepaid (CASH or BANK with paid status)
     */
    public function isPrepaid()
    {
        return in_array($this->payment_method, ['CASH', 'BANK']) && $this->payment_status === 'paid';
    }

    /**
     * Check if order is COD
     */
    public function isCOD()
    {
        return $this->payment_method === 'COD';
    }

    /**
     * Get payment method display name
     */
    public function getPaymentMethodDisplayAttribute()
    {
        return match ($this->payment_method) {
            'COD' => 'Cash on Delivery',
            'CASH' => 'Cash Payment',
            'BANK' => 'Bank Transfer',
            default => $this->payment_method
        };
    }

    /**
     * Check if order needs waybill creation
     */
    public function needsWaybillCreation(): bool
    {
        return empty($this->waybill_no) &&
            in_array($this->payment_status, ['paid']) &&
            !in_array($this->delivery_status, ['cancelled', 'refunded']);
    }



    /**
     * Check if order has active waybill retry
     */
    public function hasActiveWaybillRetry(): bool
    {
        $retryCount = $this->getWaybillRetryCount();
        $adminNotified = $this->isWaybillAdminNotified();

        return $retryCount > 0 && $retryCount < 3 && !$adminNotified;
    }

    /**
     * Get retry intervals in minutes
     */
    public static function getRetryIntervals(): array
    {
        return [30, 60, 120]; // 30min, 1h, 2h
    }

    /**
     * Get waybill retry count from activity log
     */
    public function getWaybillRetryCount(): int
    {
        return $this->activities()
            ->where('description', 'waybill_retry_attempt')
            ->count();
    }

    /**
     * Get last waybill retry time from activity log
     */
    public function getLastWaybillRetryTime(): ?\Carbon\Carbon
    {
        $lastRetry = $this->activities()
            ->where('description', 'waybill_retry_attempt')
            ->latest()
            ->first();

        return $lastRetry ? $lastRetry->created_at : null;
    }

    /**
     * Get waybill failure time from activity log
     */
    public function getWaybillFailureTime(): ?\Carbon\Carbon
    {
        $failure = $this->activities()
            ->where('description', 'waybill_creation_failed')
            ->latest()
            ->first();

        return $failure ? $failure->created_at : null;
    }

    /**
     * Check if admin has been notified from activity log
     */
    public function isWaybillAdminNotified(): bool
    {
        return $this->activities()
            ->where('description', 'waybill_admin_notified')
            ->exists();
    }

    /**
     * Check if max retries reached
     */
    public function hasMaxRetriesReached(): bool
    {
        return $this->getWaybillRetryCount() >= count(self::getRetryIntervals());
    }

    /**
     * Check if admin notification should be sent
     */
    public function shouldNotifyAdmin(): bool
    {
        $failureTime = $this->getWaybillFailureTime();

        return $this->hasMaxRetriesReached()
            && !$this->isWaybillAdminNotified()
            && $failureTime
            && $failureTime->addHours(10)->isPast();
    }
}
