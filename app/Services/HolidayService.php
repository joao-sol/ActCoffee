<?php

namespace App\Services;

use App\Models\CustomHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class HolidayService
{
    public function calculateEaster(int $year): Carbon
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    public function getFixedHolidays(int $year): Collection
    {
        return collect([
            "$year-01-01" => 'Confraternizacao Universal',
            "$year-04-21" => 'Tiradentes',
            "$year-05-01" => 'Dia do Trabalho',
            "$year-09-07" => 'Independencia do Brasil',
            "$year-10-12" => 'Nossa Senhora Aparecida',
            "$year-11-02" => 'Finados',
            "$year-11-15" => 'Proclamacao da Republica',
            "$year-11-20" => 'Dia Nacional de Zumbi e da Consciencia Negra',
            "$year-12-09" => 'Aniversario de Guarapuava e Nossa Senhora de Belem',
            "$year-12-25" => 'Natal',
        ])->map(fn (string $name, string $date): array => [
            'date' => Carbon::parse($date)->startOfDay(),
            'name' => $name,
            'source' => 'fixed',
        ])->values();
    }

    public function getMovableHolidays(int $year): Collection
    {
        $easter = $this->calculateEaster($year);

        return collect([
            [
                'date' => $easter->copy()->subDays(47),
                'name' => 'Carnaval',
                'source' => 'movable',
            ],
            [
                'date' => $easter->copy()->subDays(2),
                'name' => 'Sexta-feira Santa',
                'source' => 'movable',
            ],
            [
                'date' => $easter->copy()->addDays(60),
                'name' => 'Corpus Christi',
                'source' => 'movable',
            ],
        ]);
    }

    public function getCustomHolidays(int $year): Collection
    {
        return CustomHoliday::query()
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get()
            ->map(fn (CustomHoliday $holiday): array => [
                'date' => $holiday->date->copy()->startOfDay(),
                'name' => $holiday->name,
                'source' => 'custom',
            ]);
    }

    public function getHolidaysForYear(int $year): Collection
    {
        return $this->getFixedHolidays($year)
            ->merge($this->getMovableHolidays($year))
            ->merge($this->getCustomHolidays($year))
            ->keyBy(fn (array $holiday): string => $holiday['date']->toDateString())
            ->sortKeys()
            ->values();
    }

    public function isHoliday(CarbonInterface $date): bool
    {
        return $this->getHolidayName($date) !== null;
    }

    public function getHolidayName(CarbonInterface $date): ?string
    {
        $target = Carbon::parse($date)->toDateString();
        $holiday = $this->getHolidaysForYear((int) $date->format('Y'))
            ->first(fn (array $holiday): bool => $holiday['date']->toDateString() === $target);

        return $holiday['name'] ?? null;
    }
}
