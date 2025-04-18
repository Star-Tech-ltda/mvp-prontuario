<?php

namespace App\Observers;

use App\Models\BiometricData;
use App\Services\MetricInterpreterService;

class BiometricDataObserver
{

    public function saved(BiometricData $biometricData): void
    {
        if (!empty($biometricData->weight) && !empty($biometricData->height)) {
            MetricInterpreterService::handle([
                'weight' => $biometricData->weight,
                'height' => $biometricData->height,
            ], $biometricData->evolution_id);
        }
    }



    /**
     * Handle the BiometricData "created" event.
     */
    public function created(BiometricData $biometricData): void
    {
        //
    }

    /**
     * Handle the BiometricData "updated" event.
     */
    public function updated(BiometricData $biometricData): void
    {
        //
    }

    /**
     * Handle the BiometricData "deleted" event.
     */
    public function deleted(BiometricData $biometricData): void
    {
        //
    }

    /**
     * Handle the BiometricData "restored" event.
     */
    public function restored(BiometricData $biometricData): void
    {
        //
    }

    /**
     * Handle the BiometricData "force deleted" event.
     */
    public function forceDeleted(BiometricData $biometricData): void
    {
        //
    }
}
