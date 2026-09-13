<?php

use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\TeacherAssignmentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TimetableController;
use App\Http\Controllers\Api\StudentAttendanceController;
use App\Http\Controllers\Api\TeacherAttendanceController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\FeeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\FeeReportController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SchoolSettingController;

use Illuminate\Support\Facades\Route;













Route::apiResource('students', StudentController::class);
Route::apiResource('teachers', TeacherController::class);
Route::apiResource('staff', StaffController::class);
Route::apiResource('enrollments', EnrollmentController::class);
Route::apiResource('academic-years', AcademicYearController::class);
Route::apiResource('classes', SchoolClassController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('teacher-assignments', TeacherAssignmentController::class);
Route::apiResource('subjects', SubjectController::class);
Route::apiResource('timetables', TimetableController::class);
Route::apiResource(
  'student-attendance',
  StudentAttendanceController::class
);
Route::apiResource(
  'teacher-attendance',
  TeacherAttendanceController::class
);
Route::apiResource('exams', ExamController::class);
Route::apiResource('results', ResultController::class);
Route::apiResource('fees', FeeController::class);
Route::apiResource('payments', PaymentController::class);
Route::get('fee-reports', [FeeReportController::class, 'index']);
Route::apiResource('notices', NoticeController::class);
Route::apiResource('events', EventController::class);
// reports api
Route::get('reports/students', [
  ReportController::class,
  'students'
]);

Route::get('reports/attendance', [
  ReportController::class,
  'attendance'
]);

Route::get('reports/fees', [
  ReportController::class,
  'fees'
]);

Route::get('reports/results', [
  ReportController::class,
  'results'
]);
// school settings api 
Route::get('school-settings', [
  SchoolSettingController::class,
  'index'
]);

Route::post('school-settings', [
  SchoolSettingController::class,
  'store'
]);

Route::get('school-settings/show', [
  SchoolSettingController::class,
  'show'
]);

Route::post('school-settings/update', [
  SchoolSettingController::class,
  'update'
]);
