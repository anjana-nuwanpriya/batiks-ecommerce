<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait LogsAdminActivity
{
    use LogsActivity;

    /**
     * Configure activity logging options with sensible defaults.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => $this->getActivityDescription($eventName))
            ->useLogName($this->getLogName());
    }

    /**
     * Get the log name for this model.
     */
    protected function getLogName(): string
    {
        $modelName = strtolower(class_basename($this));
        return $modelName . '_management';
    }

    /**
     * Get activity description for the event.
     */
    protected function getActivityDescription(string $eventName): string
    {
        $modelName = class_basename($this);
        $identifier = $this->getActivityIdentifier();

        return ucfirst($eventName) . " {$modelName}" . ($identifier ? " ({$identifier})" : '');
    }

    /**
     * Get identifier for activity logging (override in models as needed).
     */
    protected function getActivityIdentifier(): ?string
    {
        // Try common identifier fields
        if (isset($this->name)) {
            return $this->name;
        }

        if (isset($this->title)) {
            return $this->title;
        }

        if (isset($this->sku)) {
            return "SKU: {$this->sku}";
        }

        if (isset($this->email)) {
            return $this->email;
        }

        return "ID: {$this->id}";
    }

    /**
     * Log a custom activity for this model.
     */
    public function logActivity(string $description, array $properties = [], string $eventName = 'custom'): void
    {
        activity($this->getLogName())
            ->causedBy(auth()->user())
            ->performedOn($this)
            ->withProperties(array_merge([
                'operation_type' => $eventName,
                'model_identifier' => $this->getActivityIdentifier(),
                'timestamp' => now()->toDateTimeString()
            ], $properties))
            ->log($description);
    }

    /**
     * Log a bulk operation activity.
     */
    public static function logBulkActivity(string $description, array $properties = [], string $batchUuid = null): void
    {
        $modelName = strtolower(class_basename(static::class));

        activity($modelName . '_management')
            ->causedBy(auth()->user())
            ->withProperties(array_merge([
                'operation_type' => 'bulk_operation',
                'batch_uuid' => $batchUuid ?: \Illuminate\Support\Str::uuid()->toString(),
                'timestamp' => now()->toDateTimeString()
            ], $properties))
            ->log($description);
    }
}
