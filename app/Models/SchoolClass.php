<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Enrollment;
use App\Models\TeacherAssignment;
use App\Models\Timetable;
use App\Models\StudentAttendance;
use App\Models\Exam;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id');
    }

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function studentAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'class_id');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'class_id');
    }
}