<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const VICTOR = 5;
    private const HELLEN = 10;
    private const CHIARA = 15;

    public function up(): void
    {
        $swappedRepayment = DB::table('coffee_duties')
            ->whereDate('duty_date', '2026-11-06')
            ->where('employee_id', self::HELLEN)
            ->where('original_employee_id', self::VICTOR)
            ->whereNull('covered_employee_id')
            ->first();

        $wrongCounterpart = DB::table('coffee_duties')
            ->whereDate('duty_date', '2026-11-12')
            ->where('employee_id', self::VICTOR)
            ->where('original_employee_id', self::HELLEN)
            ->where('status', 'scheduled')
            ->first();

        if ($swappedRepayment === null || $wrongCounterpart === null) {
            return;
        }

        DB::transaction(function () use ($swappedRepayment, $wrongCounterpart): void {
            DB::table('coffee_duties')->where('id', $swappedRepayment->id)->update([
                'covered_employee_id' => self::CHIARA,
                'notes' => $swappedRepayment->notes.PHP_EOL.'Ajuste manual: a troca com Hellen cobriu o pagamento de Chiara a Victor; Chiara assume o dia de Hellen em 12/11/2026.',
                'updated_at' => now(),
            ]);

            DB::table('coffee_duties')->where('id', $wrongCounterpart->id)->update([
                'employee_id' => self::CHIARA,
                'notes' => $wrongCounterpart->notes.PHP_EOL.'Ajuste manual: Chiara assume o dia de Hellen (e não Victor), pois foi Chiara quem trocou com Hellen em 06/11/2026.',
                'updated_at' => now(),
            ]);
        });
    }

    public function down(): void
    {
    }
};
