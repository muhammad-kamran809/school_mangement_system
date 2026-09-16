<?php

use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\FeeReportController;
use App\Http\Controllers\Api\MyParentController;
use App\Http\Controllers\Api\MyProfileController;
use App\Http\Controllers\Api\MyStudentController;
use App\Http\Controllers\Api\MyTeacherController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SchoolSettingController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherAssignmentController;
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

// Student, teacher, parent, and staff API routes.
Route::middleware('auth:sanctum')->group(function () {

    // dashboard api
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('role:Admin');

    Route::get('students', [
        StudentController::class,
        'index',
    ])->middleware('permission:students.view');

    Route::post('students', [
        StudentController::class,
        'store',
    ])->middleware('permission:students.create');

    Route::get('students/{student}', [
        StudentController::class,
        'show',
    ])->middleware('permission:students.view');

    Route::put('students/{student}', [
        StudentController::class,
        'update',
    ])->middleware('permission:students.update');

    Route::patch('students/{student}', [
        StudentController::class,
        'update',
    ])->middleware('permission:students.update');

    Route::delete('students/{student}', [
        StudentController::class,
        'destroy',
    ])->middleware('permission:students.delete');

    Route::get('teachers', [
        TeacherController::class,
        'index',
    ])->middleware('permission:teachers.view');

    Route::post('teachers', [
        TeacherController::class,
        'store',
    ])->middleware('permission:teachers.create');

    Route::get('teachers/{teacher}', [
        TeacherController::class,
        'show',
    ])->middleware('permission:teachers.view');

    Route::put('teachers/{teacher}', [
        TeacherController::class,
        'update',
    ])->middleware('permission:teachers.update');

    Route::patch('teachers/{teacher}', [
        TeacherController::class,
        'update',
    ])->middleware('permission:teachers.update');

    Route::delete('teachers/{teacher}', [
        TeacherController::class,
        'destroy',
    ])->middleware('permission:teachers.delete');

    Route::get('staff', [
        StaffController::class,
        'index',
    ])->middleware('permission:staff.view');

    Route::post('staff', [
        StaffController::class,
        'store',
    ])->middleware('permission:staff.create');

    Route::get('staff/{staff}', [
        StaffController::class,
        'show',
    ])->middleware('permission:staff.view');

    Route::put('staff/{staff}', [
        StaffController::class,
        'update',
    ])->middleware('permission:staff.update');

    Route::patch('staff/{staff}', [
        StaffController::class,
        'update',
    ])->middleware('permission:staff.update');

    Route::delete('staff/{staff}', [
        StaffController::class,
        'destroy',
    ])->middleware('permission:staff.delete');

    Route::get('parent-test', function () {
        return response()->json([
            'message' => 'Parent access successful.',
        ]);
    })->middleware('role:Parent');

    Route::get('my-student/attendance', [MyStudentController::class, 'attendance'])
        ->middleware('role:Student');

    Route::get('my-student/results', [MyStudentController::class, 'results'])
        ->middleware('role:Student');

    Route::get('my-student/fees', [MyStudentController::class, 'fees'])
        ->middleware('role:Student');

    Route::get('my-student/payments', [MyStudentController::class, 'payments'])
        ->middleware('role:Student');
});

// enrollment api with permission middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::get('enrollments', [
        EnrollmentController::class,
        'index',
    ])->middleware('permission:enrollments.view');

    Route::post('enrollments', [
        EnrollmentController::class,
        'store',
    ])->middleware('permission:enrollments.create');

    Route::get('enrollments/{enrollment}', [
        EnrollmentController::class,
        'show',
    ])->middleware('permission:enrollments.view');

    Route::put('enrollments/{enrollment}', [
        EnrollmentController::class,
        'update',
    ])->middleware('permission:enrollments.update');

    Route::patch('enrollments/{enrollment}', [
        EnrollmentController::class,
        'update',
    ])->middleware('permission:enrollments.update');

    Route::delete('enrollments/{enrollment}', [
        EnrollmentController::class,
        'destroy',
    ])->middleware('permission:enrollments.delete');
});

// academic years api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('academic-years', [
        AcademicYearController::class,
        'index',
    ])->middleware('permission:academic_years.view');

    Route::post('academic-years', [
        AcademicYearController::class,
        'store',
    ])->middleware('permission:academic_years.create');

    Route::get('academic-years/{academicYear}', [
        AcademicYearController::class,
        'show',
    ])->middleware('permission:academic_years.view');

    Route::put('academic-years/{academicYear}', [
        AcademicYearController::class,
        'update',
    ])->middleware('permission:academic_years.update');

    Route::patch('academic-years/{academicYear}', [
        AcademicYearController::class,
        'update',
    ])->middleware('permission:academic_years.update');

    Route::delete('academic-years/{academicYear}', [
        AcademicYearController::class,
        'destroy',
    ])->middleware('permission:academic_years.delete');
});

// classes api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('classes', [
        SchoolClassController::class,
        'index',
    ])->middleware('permission:classes.view');

    Route::post('classes', [
        SchoolClassController::class,
        'store',
    ])->middleware('permission:classes.create');

    Route::get('classes/{schoolClass}', [
        SchoolClassController::class,
        'show',
    ])->middleware('permission:classes.view');

    Route::put('classes/{schoolClass}', [
        SchoolClassController::class,
        'update',
    ])->middleware('permission:classes.update');

    Route::patch('classes/{schoolClass}', [
        SchoolClassController::class,
        'update',
    ])->middleware('permission:classes.update');

    Route::delete('classes/{schoolClass}', [
        SchoolClassController::class,
        'destroy',
    ])->middleware('permission:classes.delete');
});

// sections api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('sections', [
        SectionController::class,
        'index',
    ])->middleware('permission:sections.view');

    Route::post('sections', [
        SectionController::class,
        'store',
    ])->middleware('permission:sections.create');

    Route::get('sections/{section}', [
        SectionController::class,
        'show',
    ])->middleware('permission:sections.view');

    Route::put('sections/{section}', [
        SectionController::class,
        'update',
    ])->middleware('permission:sections.update');

    Route::patch('sections/{section}', [
        SectionController::class,
        'update',
    ])->middleware('permission:sections.update');

    Route::delete('sections/{section}', [
        SectionController::class,
        'destroy',
    ])->middleware('permission:sections.delete');
});

// teacher assignments api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('teacher-assignments', [
        TeacherAssignmentController::class,
        'index',
    ])->middleware('permission:teacher_assignments.view');

    Route::post('teacher-assignments', [
        TeacherAssignmentController::class,
        'store',
    ])->middleware('permission:teacher_assignments.create');

    Route::get('teacher-assignments/{teacherAssignment}', [
        TeacherAssignmentController::class,
        'show',
    ])->middleware('permission:teacher_assignments.view');

    Route::put('teacher-assignments/{teacherAssignment}', [
        TeacherAssignmentController::class,
        'update',
    ])->middleware('permission:teacher_assignments.update');

    Route::patch('teacher-assignments/{teacherAssignment}', [
        TeacherAssignmentController::class,
        'update',
    ])->middleware('permission:teacher_assignments.update');

    Route::delete('teacher-assignments/{teacherAssignment}', [
        TeacherAssignmentController::class,
        'destroy',
    ])->middleware('permission:teacher_assignments.delete');
});

// subjects api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('subjects', [
        SubjectController::class,
        'index',
    ])->middleware('permission:subjects.view');

    Route::post('subjects', [
        SubjectController::class,
        'store',
    ])->middleware('permission:subjects.create');

    Route::get('subjects/{subject}', [
        SubjectController::class,
        'show',
    ])->middleware('permission:subjects.view');

    Route::put('subjects/{subject}', [
        SubjectController::class,
        'update',
    ])->middleware('permission:subjects.update');

    Route::patch('subjects/{subject}', [
        SubjectController::class,
        'update',
    ])->middleware('permission:subjects.update');

    Route::delete('subjects/{subject}', [
        SubjectController::class,
        'destroy',
    ])->middleware('permission:subjects.delete');
});

// timetables api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('timetables', [
        TimetableController::class,
        'index',
    ])->middleware('permission:timetables.view');

    Route::post('timetables', [
        TimetableController::class,
        'store',
    ])->middleware('permission:timetables.create');

    Route::get('timetables/{timetable}', [
        TimetableController::class,
        'show',
    ])->middleware('permission:timetables.view');

    Route::put('timetables/{timetable}', [
        TimetableController::class,
        'update',
    ])->middleware('permission:timetables.update');

    Route::patch('timetables/{timetable}', [
        TimetableController::class,
        'update',
    ])->middleware('permission:timetables.update');

    Route::delete('timetables/{timetable}', [
        TimetableController::class,
        'destroy',
    ])->middleware('permission:timetables.delete');
});

// student attendance api with permission middleware

Route::middleware('auth:sanctum')->group(function () {
    Route::get('student-attendance', [
        StudentAttendanceController::class,
        'index',
    ])->middleware('permission:student_attendance.view');

    Route::post('student-attendance', [
        StudentAttendanceController::class,
        'store',
    ])->middleware('permission:student_attendance.create');

    Route::get('student-attendance/{studentAttendance}', [
        StudentAttendanceController::class,
        'show',
    ])->middleware('permission:student_attendance.view');

    Route::put('student-attendance/{studentAttendance}', [
        StudentAttendanceController::class,
        'update',
    ])->middleware('permission:student_attendance.update');

    Route::patch('student-attendance/{studentAttendance}', [
        StudentAttendanceController::class,
        'update',
    ])->middleware('permission:student_attendance.update');

    Route::delete('student-attendance/{studentAttendance}', [
        StudentAttendanceController::class,
        'destroy',
    ])->middleware('permission:student_attendance.delete');
});

// teacher attendance api with permission middleware

Route::middleware('auth:sanctum')->group(function () {

    Route::get('teacher-attendance', [
        TeacherAttendanceController::class,
        'index',
    ])->middleware('permission:teacher_attendance.view');

    Route::post('teacher-attendance', [
        TeacherAttendanceController::class,
        'store',
    ])->middleware('permission:teacher_attendance.create');

    Route::get('teacher-attendance/{teacherAttendance}', [
        TeacherAttendanceController::class,
        'show',
    ])->middleware('permission:teacher_attendance.view');

    Route::put('teacher-attendance/{teacherAttendance}', [
        TeacherAttendanceController::class,
        'update',
    ])->middleware('permission:teacher_attendance.update');

    Route::patch('teacher-attendance/{teacherAttendance}', [
        TeacherAttendanceController::class,
        'update',
    ])->middleware('permission:teacher_attendance.update');

    Route::delete('teacher-attendance/{teacherAttendance}', [
        TeacherAttendanceController::class,
        'destroy',
    ])->middleware('permission:teacher_attendance.delete');

    Route::get('my-teacher/attendance/students', [TeacherAttendanceController::class, 'students'])
        ->middleware('role:Teacher');

    Route::post('my-teacher/attendance', [TeacherAttendanceController::class, 'store'])
        ->middleware('role:Teacher');

    Route::get('my-teacher/attendance', [TeacherAttendanceController::class, 'index'])
        ->middleware('role:Teacher');
});

// exams api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('exams', [
        ExamController::class,
        'index',
    ])->middleware('permission:exams.view');

    Route::post('exams', [
        ExamController::class,
        'store',
    ])->middleware('permission:exams.create');

    Route::get('exams/{exam}', [
        ExamController::class,
        'show',
    ])->middleware('permission:exams.view');

    Route::put('exams/{exam}', [
        ExamController::class,
        'update',
    ])->middleware('permission:exams.update');

    Route::patch('exams/{exam}', [
        ExamController::class,
        'update',
    ])->middleware('permission:exams.update');

    Route::delete('exams/{exam}', [
        ExamController::class,
        'destroy',
    ])->middleware('permission:exams.delete');
});

// results api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('results', [
        ResultController::class,
        'index',
    ])->middleware('permission:results.view');

    Route::post('results', [
        ResultController::class,
        'store',
    ])->middleware('permission:results.create');

    Route::get('results/{result}', [
        ResultController::class,
        'show',
    ])->middleware('permission:results.view');

    Route::put('results/{result}', [
        ResultController::class,
        'update',
    ])->middleware('permission:results.update');

    Route::patch('results/{result}', [
        ResultController::class,
        'update',
    ])->middleware('permission:results.update');

    Route::delete('results/{result}', [
        ResultController::class,
        'destroy',
    ])->middleware('permission:results.delete');
});

// fees api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('fees', [
        FeeController::class,
        'index',
    ])->middleware('permission:fees.view');

    Route::post('fees', [
        FeeController::class,
        'store',
    ])->middleware('permission:fees.create');

    Route::get('fees/{fee}', [
        FeeController::class,
        'show',
    ])->middleware('permission:fees.view');

    Route::put('fees/{fee}', [
        FeeController::class,
        'update',
    ])->middleware('permission:fees.update');

    Route::patch('fees/{fee}', [
        FeeController::class,
        'update',
    ])->middleware('permission:fees.update');

    Route::delete('fees/{fee}', [
        FeeController::class,
        'destroy',
    ])->middleware('permission:fees.delete');
});

// payments api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('payments', [
        PaymentController::class,
        'index',
    ])->middleware('permission:payments.view');

    Route::post('payments', [
        PaymentController::class,
        'store',
    ])->middleware('permission:payments.create');

    Route::get('payments/{payment}', [
        PaymentController::class,
        'show',
    ])->middleware('permission:payments.view');

    Route::put('payments/{payment}', [
        PaymentController::class,
        'update',
    ])->middleware('permission:payments.update');

    Route::patch('payments/{payment}', [
        PaymentController::class,
        'update',
    ])->middleware('permission:payments.update');

    Route::delete('payments/{payment}', [
        PaymentController::class,
        'destroy',
    ])->middleware('permission:payments.delete');
});

Route::get('fee-reports', [
    FeeReportController::class,
    'index',
])->middleware([
    'auth:sanctum',
    'permission:fee_reports.view',
]);
// notices api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('notices', [
        NoticeController::class,
        'index',
    ])->middleware('permission:notices.view');

    Route::post('notices', [
        NoticeController::class,
        'store',
    ])->middleware('permission:notices.create');

    Route::get('notices/{notice}', [
        NoticeController::class,
        'show',
    ])->middleware('permission:notices.view');

    Route::put('notices/{notice}', [
        NoticeController::class,
        'update',
    ])->middleware('permission:notices.update');

    Route::patch('notices/{notice}', [
        NoticeController::class,
        'update',
    ])->middleware('permission:notices.update');

    Route::delete('notices/{notice}', [
        NoticeController::class,
        'destroy',
    ])->middleware('permission:notices.delete');
});

// events api with permission middleware
Route::middleware('auth:sanctum')->group(function () {

    Route::get('events', [
        EventController::class,
        'index',
    ])->middleware('permission:events.view');

    Route::post('events', [
        EventController::class,
        'store',
    ])->middleware('permission:events.create');

    Route::get('events/{event}', [
        EventController::class,
        'show',
    ])->middleware('permission:events.view');

    Route::put('events/{event}', [
        EventController::class,
        'update',
    ])->middleware('permission:events.update');

    Route::patch('events/{event}', [
        EventController::class,
        'update',
    ])->middleware('permission:events.update');

    Route::delete('events/{event}', [
        EventController::class,
        'destroy',
    ])->middleware('permission:events.delete');
});

// reports api
// We don't need create/update/delete permissions.

Route::get('reports/students', [
    ReportController::class,
    'index',
])
    ->middleware([
        'auth:sanctum',
        'permission:student_reports.view',
    ]);

Route::get('reports/attendance', [
    ReportController::class,
    'index',
])
    ->middleware([
        'auth:sanctum',
        'permission:attendance_reports.view',
    ]);

Route::get('reports/results', [
    ReportController::class,
    'index',
])
    ->middleware([
        'auth:sanctum',
        'permission:result_reports.view',
    ]);

// school settings api
Route::middleware('auth:sanctum')->group(function () {

    Route::get('school-settings', [
        SchoolSettingController::class,
        'index',
    ])->middleware('permission:school_settings.view');

    Route::post('school-settings', [
        SchoolSettingController::class,
        'store',
    ])->middleware('permission:school_settings.update');

    Route::get('school-settings/show', [
        SchoolSettingController::class,
        'show',
    ])->middleware('permission:school_settings.view');

    Route::post('school-settings/update', [
        SchoolSettingController::class,
        'update',
    ])->middleware('permission:school_settings.update');
});

// authentication api
Route::post('login', [
    AuthController::class,
    'login',
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('me', [
        MyProfileController::class,
        'me',
    ]);

    Route::get('my-profile/student', [
        MyProfileController::class,
        'myStudent',
    ])->middleware('role:Student');

    Route::get('my-children', [
        MyProfileController::class,
        'myChildren',
    ])->middleware('role:Parent');

    Route::get('my-profile/my-children', [
        MyProfileController::class,
        'myChildren',
    ])->middleware('role:Parent');

    // Parent ownership routes
    Route::get('my-parent/children', [MyParentController::class, 'children'])
        ->middleware('role:Parent');

    Route::get('my-parent/children/{student}/attendance', [MyParentController::class, 'attendance'])
        ->middleware('role:Parent');

    Route::get('my-parent/children/{student}/results', [MyParentController::class, 'results'])
        ->middleware('role:Parent');

    Route::get('my-parent/children/{student}/fees', [MyParentController::class, 'fees'])
        ->middleware('role:Parent');

    Route::get('my-parent/children/{student}/payments', [MyParentController::class, 'payments'])
        ->middleware('role:Parent');

    Route::get('my-teacher/assignments', [MyTeacherController::class, 'assignments'])
        ->middleware('role:Teacher');

    Route::get('my-teacher/students', [MyTeacherController::class, 'students'])
        ->middleware('role:Teacher');

    Route::post('logout', [
        AuthController::class,
        'logout',
    ]);

    Route::get('admin-test', function () {
        return response()->json([
            'message' => 'Admin access successful.',
        ]);
    })->middleware('role:Admin');

    Route::get('teacher-test', function () {
        return response()->json([
            'message' => 'Teacher access successful.',
        ]);
    })->middleware('role:Teacher');

    Route::get('student-test', function () {
        return response()->json([
            'message' => 'Student access successful.',
        ]);
    })->middleware('role:Student');

    Route::get('staff-test', function () {
        return response()->json([
            'message' => 'Staff access successful.',
        ]);
    })->middleware('role:Staff');
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('users', [
        UserController::class,
        'index',
    ])->middleware('permission:users.view');

    Route::post('users', [
        UserController::class,
        'store',
    ])->middleware('permission:users.create');

    Route::get('users/{user}', [
        UserController::class,
        'show',
    ])->middleware('permission:users.view');

    Route::put('users/{user}', [
        UserController::class,
        'update',
    ])->middleware('permission:users.update');

    Route::patch('users/{user}', [
        UserController::class,
        'update',
    ])->middleware('permission:users.update');

    Route::delete('users/{user}', [
        UserController::class,
        'destroy',
    ])->middleware('permission:users.delete');

    // User profile management routes
    Route::post('users/{user}/connect-teacher', [
        UserProfileController::class,
        'connectTeacher',
    ])->middleware('permission:users.update');

    Route::post('users/{user}/connect-student', [
        UserProfileController::class,
        'connectStudent',
    ])->middleware('permission:users.update');

    Route::post('users/{user}/connect-staff', [
        UserProfileController::class,
        'connectStaff',
    ])->middleware('permission:users.update');
});
