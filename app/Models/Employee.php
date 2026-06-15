<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'queue_position',
        'hired_at',
        'dismissed_at',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'hired_at' => 'date',
            'dismissed_at' => 'date',
        ];
    }

    public function vacations(): HasMany
    {
        return $this->hasMany(Vacation::class);
    }

    public function coffeeDuties(): HasMany
    {
        return $this->hasMany(CoffeeDuty::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function isOnVacation(CarbonInterface $date): bool
    {
        return $this->vacations()
            ->whereDate('start_date', '<=', $date->toDateString())
            ->whereDate('end_date', '>=', $date->toDateString())
            ->exists();
    }
}
