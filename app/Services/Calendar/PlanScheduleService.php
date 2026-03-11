<?php

namespace App\Services\Calendar;

use App\Models\Calendar\Plan;
use Carbon\Carbon;

readonly class PlanScheduleService
{
    public function matchesDate(array $schedule, Carbon $startDate, ?Carbon $endDate, Carbon $date): bool
    {
        $day = $date->copy()->startOfDay();
        $start = $startDate->copy()->startOfDay();

        if ($day->lt($start)) {
            return false;
        }

        if ($endDate !== null && $day->gt($endDate->copy()->startOfDay())) {
            return false;
        }

        return match ($schedule['type']) {
            'daily' => true,
            'weekly' => in_array($day->dayOfWeekIso - 1, $schedule['days']),
            'interval' => $day->diffInDays($start) % $schedule['every'] === 0,
            'monthly_dates' => in_array($day->day, $schedule['days']),
            'monthly_weekday' => $this->matchesMonthlyWeekday($day, $schedule['week'], $schedule['weekday']),
            default => false,
        };
    }

    public function getMatchingDatesInRange(Plan $plan, Carbon $from, Carbon $to): array
    {
        $start = $from->gt($plan->start_date) ? $from->copy()->startOfDay() : $plan->start_date->copy()->startOfDay();
        $end = $plan->end_date
            ? ($to->lt($plan->end_date) ? $to->copy()->startOfDay() : $plan->end_date->copy()->startOfDay())
            : $to->copy()->startOfDay();

        if ($start->gt($end)) {
            return [];
        }

        $dates = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($this->matchesDate($plan->schedule, $plan->start_date, $plan->end_date, $current->copy())) {
                $dates[] = $current->toDateString();
            }
            $current->addDay();
        }

        return $dates;
    }

    private function matchesMonthlyWeekday(Carbon $date, int $week, int $weekday): bool
    {
        // weekday: 0=Mon..6=Sun; Carbon dayOfWeekIso: 1=Mon..7=Sun
        if ($date->dayOfWeekIso - 1 !== $weekday) {
            return false;
        }

        if ($week === -1) {
            return $date->day + 7 > $date->daysInMonth;
        }

        return intdiv($date->day - 1, 7) + 1 === $week;
    }
}
