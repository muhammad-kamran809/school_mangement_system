<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Dashboard
            'dashboard.view',

            // Academic
            'academic_years.view',
            'academic_years.create',
            'academic_years.update',
            'academic_years.delete',

            'classes.view',
            'classes.create',
            'classes.update',
            'classes.delete',

            'sections.view',
            'sections.create',
            'sections.update',
            'sections.delete',

            'subjects.view',
            'subjects.create',
            'subjects.update',
            'subjects.delete',

            // People
            'students.view',
            'students.create',
            'students.update',
            'students.delete',

            'teachers.view',
            'teachers.create',
            'teachers.update',
            'teachers.delete',

            'staff.view',
            'staff.create',
            'staff.update',
            'staff.delete',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',

            // Academic Management
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'enrollments.delete',

            'teacher_assignments.view',
            'teacher_assignments.create',
            'teacher_assignments.update',
            'teacher_assignments.delete',

            'timetables.view',
            'timetables.create',
            'timetables.update',
            'timetables.delete',

            // Attendance
            'student_attendance.view',
            'student_attendance.create',
            'student_attendance.update',
            'student_attendance.delete',

            'teacher_attendance.view',
            'teacher_attendance.create',
            'teacher_attendance.update',
            'teacher_attendance.delete',

            // Exams
            'exams.view',
            'exams.create',
            'exams.update',
            'exams.delete',

            'results.view',
            'results.create',
            'results.update',
            'results.delete',

            // Finance
            'fees.view',
            'fees.create',
            'fees.update',
            'fees.delete',

            'payments.view',
            'payments.create',
            'payments.update',
            'payments.delete',

            'fee_reports.view',

            // Communication
            'notices.view',
            'notices.create',
            'notices.update',
            'notices.delete',

            'events.view',
            'events.create',
            'events.update',
            'events.delete',

            // Reports
            'student_reports.view',
            'attendance_reports.view',
            'result_reports.view',

            // Settings
            'school_settings.view',
            'school_settings.update',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(Permission::all());

        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */
        $teacher = Role::firstOrCreate([
            'name' => 'Teacher',
            'guard_name' => 'web',
        ]);

        $teacher->syncPermissions([
            'students.view',

            'classes.view',
            'sections.view',
            'subjects.view',

            'enrollments.view',

            'teacher_assignments.view',

            'timetables.view',

            'student_attendance.view',
            'student_attendance.create',
            'student_attendance.update',

            'teacher_attendance.view',
            'teacher_attendance.create',
            'teacher_attendance.update',

            'exams.view',

            'results.view',
            'results.create',
            'results.update',

            'notices.view',
            'events.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $student = Role::firstOrCreate([
            'name' => 'Student',
            'guard_name' => 'web',
        ]);

        $student->syncPermissions([
            // 'students.view',

            'classes.view',
            'sections.view',
            'subjects.view',

            'enrollments.view',

            'timetables.view',

            'student_attendance.view',

            'exams.view',

            'results.view',

            'fees.view',
            'payments.view',

            'notices.view',
            'events.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Parent
        |--------------------------------------------------------------------------
        */
        $parent = Role::firstOrCreate([
            'name' => 'Parent',
            'guard_name' => 'web',
        ]);

        $parent->syncPermissions([
            // 'students.view',

            'classes.view',
            'sections.view',

            // 'enrollments.view',

            'timetables.view',

            // 'student_attendance.view',

            'exams.view',

            // 'results.view',

            // 'fees.view',
            // 'payments.view',

            'notices.view',
            'events.view',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $staff = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'web',
        ]);

        $staff->syncPermissions([
            'students.view',

            'teachers.view',

            'staff.view',

            'classes.view',
            'sections.view',

            'subjects.view',

            'enrollments.view',

            'timetables.view',

            'student_attendance.view',

            'teacher_attendance.view',

            'fees.view',
            'payments.view',

            'notices.view',
            'events.view',
        ]);
    }
}
