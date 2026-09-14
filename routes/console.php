<?php

use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('act-coffee:concluir-pendentes', function (ScheduleGeneratorService $schedule): void {
    $schedule->ensureDutyForDate(Carbon::today());
    $completed = $schedule->completeExpiredDuties();

    $this->info($completed.' lavagem(ns) concluída(s) automaticamente.');
})->purpose('Conclui lavagens após o prazo de três dias úteis para trocas');

Schedule::command('act-coffee:concluir-pendentes')
    ->dailyAt('00:05')
    ->withoutOverlapping();
