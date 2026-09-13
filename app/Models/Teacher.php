<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Models\TeacherAttendance;


class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'qualification',
        'joining_date',
        'salary',
        'status',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class);
    }
}