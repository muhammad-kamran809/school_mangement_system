<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Enrollment;
use App\Models\StudentAttendance;
use App\Models\Result;
use App\Models\Fee;
use App\Models\Payment;


class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'guardian_name',
        'guardian_phone',
        'status',
        'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}