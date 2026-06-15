<?php

namespace App\Services;

use App\Models\CoffeeDuty;
use Carbon\CarbonInterface;

class HistoryCleanupService
{
    public function removeOlderThanThirtyDays(?CarbonInterface $referenceDate = null): int
    {
        $referenceDate ??= now();

        return CoffeeDuty::query()
            ->whereDate('duty_date', '<', $referenceDate->copy()->subDays(30)->toDateString())
            ->delete();
    }
}
