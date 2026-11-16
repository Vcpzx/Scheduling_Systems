<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = [
        'semester',
        'academic_year',
        'starts_on',
        'ends_on',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
        ];
    }

    public static function current(): ?self
    {
        $today = now()->toDateString();

        $term = static::whereDate('starts_on', '<=', $today)
            ->whereDate('ends_on', '>=', $today)
            ->orderByDesc('starts_on')
            ->first()
            ?? static::whereDate('starts_on', '>', $today)
                ->orderBy('starts_on')
                ->first()
            ?? static::whereDate('ends_on', '<', $today)
                ->orderByDesc('ends_on')
                ->first();

        if ($term) {
            return $term;
        }

        $latestSchedule = ClassSchedule::orderByDesc('id')->first();

        if (!$latestSchedule) {
            return null;
        }

        $term = new static();
        $term->setAttribute('semester', $latestSchedule->semester);
        $term->setAttribute('academic_year', $latestSchedule->academic_year);

        return $term;
    }
}
