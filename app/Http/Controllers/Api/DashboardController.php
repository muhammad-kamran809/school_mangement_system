<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Validate academic year filter
        $validated = $request->validate([
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Academic Year
        |--------------------------------------------------------------------------
        */

        $academicYear = null;

        if (! empty($validated['academic_year_id'])) {
            $academicYear = AcademicYear::find(
                $validated['academic_year_id']
            );
        } else {
            // Automatically use active academic year if available
            $academicYear = AcademicYear::where('status', 'active')->first();
        }

        $academicYearId = $academicYear?->id;

        /*
        |--------------------------------------------------------------------------
        | Students
        |--------------------------------------------------------------------------
        */

        $studentsQuery = Student::query();

        if ($academicYearId) {
            $studentsQuery->whereHas('enrollments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $studentsCount = $studentsQuery->count();

        /*
        |--------------------------------------------------------------------------
        | Teachers
        |--------------------------------------------------------------------------
        */

        $teachersQuery = Teacher::query();

        if ($academicYearId) {
            $teachersQuery->whereHas('assignments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $teachersCount = $teachersQuery->count();

        /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

        $staffCount = Staff::count();

        /*
        |--------------------------------------------------------------------------
        | Classes
        |--------------------------------------------------------------------------
        */

        $classesQuery = SchoolClass::query();

        if ($academicYearId) {
            $classesQuery->whereHas('enrollments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $classesCount = $classesQuery->count();

        /*
        |--------------------------------------------------------------------------
        | Sections
        |--------------------------------------------------------------------------
        */

        $sectionsQuery = Section::query();

        if ($academicYearId) {
            $sectionsQuery->whereHas('enrollments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $sectionsCount = $sectionsQuery->count();

        /*
        |--------------------------------------------------------------------------
        | Subjects
        |--------------------------------------------------------------------------
        */

        $subjectsCount = Subject::count();

        /*
        |--------------------------------------------------------------------------
        | Fees
        |--------------------------------------------------------------------------
        */

        $feesQuery = Fee::query();

        if ($academicYearId) {
            $feesQuery->where('academic_year_id', $academicYearId);
        }

        $feesTotal = $feesQuery->sum('amount');

        /*
|--------------------------------------------------------------------------
| Attendance Statistics
|--------------------------------------------------------------------------
*/

        $attendanceQuery = StudentAttendance::query();

        if ($academicYearId) {
            $attendanceQuery->whereHas('student.enrollments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $attendanceTotal = $attendanceQuery->count();

        $attendancePresent = (clone $attendanceQuery)
            ->where('status', 'present')
            ->count();

        $attendanceAbsent = (clone $attendanceQuery)
            ->where('status', 'absent')
            ->count();

        $attendanceLate = (clone $attendanceQuery)
            ->where('status', 'late')
            ->count();

        /*
|--------------------------------------------------------------------------
| Exam Statistics
|--------------------------------------------------------------------------
*/

        $examsQuery = Exam::query();

        if ($academicYearId) {
            $examsQuery->where('academic_year_id', $academicYearId);
        }

        $examsCount = $examsQuery->count();

        $resultsQuery = Result::query();

        if ($academicYearId) {
            $resultsQuery->whereHas('exam', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $resultsCount = $resultsQuery->count();

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query();

        if ($academicYearId) {
            $paymentsQuery->whereHas('fee', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $paymentsTotal = $paymentsQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Remaining Fees
        |--------------------------------------------------------------------------
        */

        $remainingFees = $feesTotal - $paymentsTotal;
        $collectionPercentage = $feesTotal > 0
            ? round(($paymentsTotal / $feesTotal) * 100, 2)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Pending Fees
        |--------------------------------------------------------------------------
        */

        $pendingFeesQuery = Fee::query()
            ->whereIn('status', [
                'pending',
                'unpaid',
                'partial',
            ]);

        if ($academicYearId) {
            $pendingFeesQuery->where(
                'academic_year_id',
                $academicYearId
            );
        }

        $pendingFees = $pendingFeesQuery->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | Recent Students
        |--------------------------------------------------------------------------
        */

        $recentStudentsQuery = Student::query()
            ->latest();

        if ($academicYearId) {
            $recentStudentsQuery->whereHas('enrollments', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $recentStudents = $recentStudentsQuery
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Payments
        |--------------------------------------------------------------------------
        */

        $recentPaymentsQuery = Payment::with([
            'student',
            'fee',
        ])
            ->latest('payment_date');

        if ($academicYearId) {
            $recentPaymentsQuery->whereHas('fee', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            });
        }

        $recentPayments = $recentPaymentsQuery
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' => 'Dashboard data retrieved successfully.',

            'academic_year' => $academicYear,

            'data' => [

                'counts' => [
                    'students' => $studentsCount,
                    'teachers' => $teachersCount,
                    'staff' => $staffCount,
                    'classes' => $classesCount,
                    'sections' => $sectionsCount,
                    'subjects' => $subjectsCount,
                ],

                'attendance' => [
                    'total' => $attendanceTotal,
                    'present' => $attendancePresent,
                    'absent' => $attendanceAbsent,
                    'late' => $attendanceLate,
                ],

                'exams' => [
                    'total' => $examsCount,
                    'results' => $resultsCount,
                ],

                'fees' => [
                    'total' => $feesTotal,
                    'paid' => $paymentsTotal,
                    'remaining' => $remainingFees,
                    'pending' => $pendingFees,
                    'collection_percentage' => $collectionPercentage,
                ],

                'recent_students' => $recentStudents,

                'recent_payments' => $recentPayments,
            ],
        ]);
    }
}
