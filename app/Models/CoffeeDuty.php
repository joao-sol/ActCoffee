<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoffeeDuty extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'employee_id',
        'original_employee_id',
        'duty_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'duty_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function originalEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'original_employee_id');
    }
}
