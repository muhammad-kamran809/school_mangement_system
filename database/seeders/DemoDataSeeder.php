<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Fee;
use App\Models\Notice;
use App\Models\Payment;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentParent;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAssignment;
use App\Models\TeacherAttendance;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::updateOrCreate(
            ['name' => '2026-2027'],
            ['start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => 'active']
        );

        $previousYear = AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            ['start_date' => '2025-04-01', 'end_date' => '2026-03-31', 'status' => 'inactive']
        );

        $classes = collect(['Grade 1', 'Grade 2', 'Grade 3'])->mapWithKeys(function (string $name): array {
            return [$name => SchoolClass::updateOrCreate(
                ['name' => $name],
                ['description' => "$name demo class", 'status' => 'active']
            )];
        });

        $sections = collect();
        foreach ($classes as $schoolClass) {
            foreach (['A', 'B'] as $name) {
                $sections->push(Section::updateOrCreate(
                    ['class_id' => $schoolClass->id, 'name' => $name],
                    ['capacity' => 30, 'status' => 'active']
                ));
            }
        }

        $subjects = collect([
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'Social Studies', 'code' => 'SOC'],
            ['name' => 'Computer Science', 'code' => 'CS'],
        ])->map(fn (array $subject): Subject => Subject::updateOrCreate(
            ['code' => $subject['code']],
            ['name' => $subject['name'], 'description' => "{$subject['name']} demo subject", 'status' => 'active']
        ));

        $people = [
            'teacher1' => ['name' => 'Amina Khan', 'email' => 'teacher1@school.test', 'role' => 'Teacher'],
            'teacher2' => ['name' => 'Daniel Smith', 'email' => 'teacher2@school.test', 'role' => 'Teacher'],
            'student1' => ['name' => 'Noah Johnson', 'email' => 'student1@school.test', 'role' => 'Student'],
            'student2' => ['name' => 'Sophia Williams', 'email' => 'student2@school.test', 'role' => 'Student'],
            'student3' => ['name' => 'Liam Brown', 'email' => 'student3@school.test', 'role' => 'Student'],
            'parent1' => ['name' => 'Michael Johnson', 'email' => 'parent1@school.test', 'role' => 'Parent'],
            'parent2' => ['name' => 'Olivia Brown', 'email' => 'parent2@school.test', 'role' => 'Parent'],
            'staff1' => ['name' => 'Grace Wilson', 'email' => 'staff1@school.test', 'role' => 'Staff'],
        ];

        $users = collect($people)->mapWithKeys(function (array $person, string $key): array {
            $user = User::updateOrCreate(
                ['email' => $person['email']],
                ['name' => $person['name'], 'password' => Hash::make('password')]
            );
            $user->assignRole($person['role']);

            return [$key => $user];
        });

        $teachers = collect([
            ['key' => 'teacher1', 'qualification' => 'B.Ed', 'salary' => 42000],
            ['key' => 'teacher2', 'qualification' => 'M.Sc', 'salary' => 48000],
        ])->map(fn (array $teacher): Teacher => Teacher::updateOrCreate(
            ['user_id' => $users[$teacher['key']]->id],
            [
                'name' => $users[$teacher['key']]->name,
                'email' => $users[$teacher['key']]->email,
                'phone' => '+1555000'.($users[$teacher['key']]->id + 10),
                'gender' => 'other',
                'date_of_birth' => '1988-06-15',
                'address' => 'Demo staff address',
                'qualification' => $teacher['qualification'],
                'joining_date' => '2024-08-01',
                'salary' => $teacher['salary'],
                'status' => 'active',
            ]
        ));

        $staff = Staff::updateOrCreate(
            ['user_id' => $users['staff1']->id],
            [
                'name' => $users['staff1']->name,
                'email' => $users['staff1']->email,
                'phone' => '+1555000300',
                'gender' => 'female',
                'date_of_birth' => '1990-02-20',
                'address' => 'Demo staff address',
                'designation' => 'Office Manager',
                'department' => 'Administration',
                'joining_date' => '2023-09-01',
                'salary' => 36000,
                'status' => 'active',
            ]
        );

        $parents = collect([
            ['key' => 'parent1', 'phone' => '+1555000401', 'address' => '12 Oak Street'],
            ['key' => 'parent2', 'phone' => '+1555000402', 'address' => '24 Pine Street'],
        ])->map(fn (array $parent): StudentParent => StudentParent::updateOrCreate(
            ['user_id' => $users[$parent['key']]->id],
            ['name' => $users[$parent['key']]->name, 'phone' => $parent['phone'], 'address' => $parent['address']]
        ));

        $students = collect([
            ['key' => 'student1', 'parent' => 0, 'class' => 'Grade 1', 'section' => 'A'],
            ['key' => 'student2', 'parent' => 0, 'class' => 'Grade 1', 'section' => 'A'],
            ['key' => 'student3', 'parent' => 1, 'class' => 'Grade 2', 'section' => 'B'],
        ])->map(fn (array $student): Student => Student::updateOrCreate(
            ['user_id' => $users[$student['key']]->id],
            [
                'student_parents_id' => $parents[$student['parent']]->id,
                'name' => $users[$student['key']]->name,
                'email' => $users[$student['key']]->email,
                'phone' => '+1555000'.($users[$student['key']]->id + 20),
                'gender' => 'other',
                'date_of_birth' => '2014-05-10',
                'address' => 'Demo student address',
                'guardian_name' => $parents[$student['parent']]->name,
                'guardian_phone' => $parents[$student['parent']]->phone,
                'status' => 'active',
            ]
        ));

        $class = $classes['Grade 1'];
        $section = $sections->first(fn (Section $item): bool => $item->class_id === $class->id && $item->name === 'A');

        foreach ($students as $index => $student) {
            Enrollment::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
                [
                    'class_id' => $class->id,
                    'section_id' => $section->id,
                    'roll_no' => 'G1-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'enrollment_date' => '2026-04-05',
                    'status' => 'active',
                ]
            );

            StudentAttendance::updateOrCreate(
                ['student_id' => $student->id, 'date' => '2026-09-01'],
                ['class_id' => $class->id, 'section_id' => $section->id, 'status' => $index === 2 ? 'late' : 'present', 'remarks' => 'Demo attendance']
            );

            $fee = Fee::updateOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYear->id, 'fee_type' => 'Tuition'],
                ['amount' => 500, 'due_date' => '2026-09-15', 'status' => $index === 0 ? 'paid' : 'pending', 'description' => 'September tuition fee']
            );

            if ($index === 0) {
                Payment::updateOrCreate(
                    ['receipt_number' => 'DEMO-REC-001'],
                    ['fee_id' => $fee->id, 'student_id' => $student->id, 'amount' => 500, 'payment_date' => '2026-09-05', 'payment_method' => 'online', 'remarks' => 'Demo payment']
                );
            }
        }

        foreach ($teachers as $index => $teacher) {
            TeacherAttendance::updateOrCreate(
                ['teacher_id' => $teacher->id, 'date' => '2026-09-01'],
                ['status' => $index === 1 ? 'late' : 'present', 'remarks' => 'Demo attendance']
            );
        }

        foreach ($teachers as $teacherIndex => $teacher) {
            foreach ($subjects->take(2) as $subjectIndex => $subject) {
                TeacherAssignment::updateOrCreate(
                    ['teacher_id' => $teacher->id, 'academic_year_id' => $academicYear->id, 'class_id' => $class->id, 'section_id' => $section->id, 'subject_id' => $subject->id],
                    []
                );

                Timetable::updateOrCreate(
                    ['academic_year_id' => $academicYear->id, 'class_id' => $class->id, 'section_id' => $section->id, 'day' => $teacherIndex === 0 ? 'Monday' : 'Tuesday', 'start_time' => $subjectIndex === 0 ? '09:00:00' : '10:00:00'],
                    ['subject_id' => $subject->id, 'teacher_id' => $teacher->id, 'end_time' => $subjectIndex === 0 ? '09:45:00' : '10:45:00', 'room' => 'Room '.($teacherIndex + 1)]
                );
            }
        }

        $exam = Exam::updateOrCreate(
            ['name' => 'Midterm Examination', 'academic_year_id' => $academicYear->id, 'class_id' => $class->id],
            ['start_date' => '2026-09-20', 'end_date' => '2026-09-25', 'status' => 'upcoming']
        );

        foreach ($students as $studentIndex => $student) {
            foreach ($subjects->take(2) as $subject) {
                Result::updateOrCreate(
                    ['exam_id' => $exam->id, 'student_id' => $student->id, 'subject_id' => $subject->id],
                    ['marks' => 75 + $studentIndex, 'total_marks' => 100, 'grade' => 'B+', 'remarks' => 'Demo result']
                );
            }
        }

        Notice::updateOrCreate(
            ['title' => 'Welcome to the new academic year'],
            ['description' => 'This is demo notice data for testing.', 'publish_date' => '2026-04-01', 'expiry_date' => '2027-03-31', 'status' => 'published']
        );

        Event::updateOrCreate(
            ['title' => 'Annual Sports Day'],
            ['description' => 'Demo school event.', 'event_date' => '2026-10-15', 'start_time' => '09:00:00', 'end_time' => '15:00:00', 'location' => 'Main Ground', 'status' => 'scheduled']
        );

        SchoolSetting::updateOrCreate(
            ['school_name' => 'Demo International School'],
            ['email' => 'office@demo-school.test', 'phone' => '+1555000100', 'address' => '100 School Road', 'website' => 'https://demo-school.test', 'principal_name' => 'Dr. Jane Doe']
        );

        unset($previousYear, $staff);
    }
}
