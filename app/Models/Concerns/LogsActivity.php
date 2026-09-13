<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * FR-1.4: records who created/edited/deleted content and when.
 *
 * Applied to content models (not pivots, not Media — Spatie owns that table).
 * Logs a flat field => "description" map so the Filament view modal can
 * render it straight into a KeyValue component without extra transforms.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model): void {
            $model->recordActivity('created', $model->activitySnapshot($model->getAttributes()));
        });

        static::updated(function ($model): void {
            $changes = [];

            foreach ($model->getChanges() as $key => $new) {
                if ($key === 'updated_at' || $key === 'deleted_at' || in_array($key, $model->activityRedactedKeys(), true)) {
                    continue;
                }

                $changes[$key] = sprintf(
                    '%s → %s',
                    $model->activityStringify($model->getOriginal($key)),
                    $model->activityStringify($new)
                );
            }

            if ($changes !== []) {
                $model->recordActivity('updated', $changes);
            }
        });

        static::deleted(function ($model): void {
            $isSoftDelete = method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting();

            $model->recordActivity(
                $isSoftDelete ? 'archived' : 'deleted',
                $model->activitySnapshot($model->getAttributes())
            );
        });

        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model): void {
                $model->recordActivity('restored', $model->activitySnapshot($model->getAttributes()));
            });
        }
    }

    protected function recordActivity(string $action, array $changes): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => static::class,
            'subject_id' => $this->getKey(),
            'subject_label' => $this->activityLabel(),
            'changes' => $changes,
        ]);
    }

    /** @return array<string, string> */
    protected function activitySnapshot(array $attributes): array
    {
        $snapshot = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, ['id', 'created_at', 'updated_at', ...$this->activityRedactedKeys()], true)) {
                continue;
            }

            $snapshot[$key] = $this->activityStringify($value);
        }

        return $snapshot;
    }

    protected function activityStringify(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '(empty)';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value) ?: '(empty)';
        }

        return (string) $value;
    }

    /** Fields never written to the log, even hashed. */
    protected function activityRedactedKeys(): array
    {
        return ['password', 'remember_token'];
    }

    /** Best-effort human-readable label so the log stays legible after the record is gone. */
    protected function activityLabel(): ?string
    {
        foreach (['title', 'name', 'headline', 'customer_name', 'section_key', 'label'] as $attribute) {
            if (! empty($this->{$attribute})) {
                return (string) $this->{$attribute};
            }
        }

        return null;
    }
}
