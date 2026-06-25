<?php

use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\CustomHolidayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\VacationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PublicScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicScheduleController::class, 'index'])->name('home');
Route::get('/escala', [PublicScheduleController::class, 'schedule'])->name('schedule');
Route::patch('/escala/{date}/concluir', [PublicScheduleController::class, 'complete'])->name('schedule.complete');
Route::patch('/escala/{date}/trocar', [PublicScheduleController::class, 'swap'])->name('schedule.swap');
Route::get('/historico', [PublicScheduleController::class, 'history'])->name('history');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::resource('funcionarios', EmployeeController::class)
            ->except(['show', 'destroy'])
            ->parameters(['funcionarios' => 'employee']);
        Route::patch('funcionarios/{employee}/inativar', [EmployeeController::class, 'deactivate'])
            ->name('funcionarios.deactivate');

        Route::resource('ferias', VacationController::class)
            ->except(['show'])
            ->parameters(['ferias' => 'vacation']);

        Route::resource('feriados-personalizados', CustomHolidayController::class)
            ->except(['show'])
            ->parameters(['feriados-personalizados' => 'custom_holiday']);

        Route::get('/escala', [AdminScheduleController::class, 'index'])->name('escala.index');
        Route::patch('/escala/{date}/concluir', [AdminScheduleController::class, 'complete'])->name('escala.complete');
        Route::patch('/escala/{date}/trocar', [AdminScheduleController::class, 'swap'])->name('escala.swap');
    });
