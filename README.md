# School Management System - Laravel REST API

A modern, robust, and secure School Management System backend API built with **Laravel 12 (PHP 8.3)**, **Laravel Sanctum**, and **Spatie Laravel-Permission**.

---

## 📌 Table of Contents

- [Features](#-features)
- [Tech Stack & Requirements](#-tech-stack--requirements)
- [Installation & Quick Start](#-installation--quick-start)
- [Default Demo Credentials](#-default-demo-credentials)
- [Authentication & Usage](#-authentication--usage)
- [Unified Pagination API](#-unified-pagination-api)
- [API Endpoints Reference](#-api-endpoints-reference)
  - [Authentication & Profile](#1-authentication--profile)
  - [Academic Management](#2-academic-management)
  - [People Management](#3-people-management)
  - [Timetable](#4-timetable)
  - [Attendance](#5-attendance)
  - [Exams & Results](#6-exams--results)
  - [Fees & Payments](#7-fees--payments)
  - [Notices & Events](#8-notices--events)
  - [Reports](#9-reports)
  - [Dashboard & Settings](#10-dashboard--settings)
  - [User & Role Management](#11-user--role-management)
- [Testing & Quality Assurance](#-testing--quality-assurance)

---

## 🚀 Features

- **Role-Based Access Control (RBAC)**: Fine-grained roles (`Admin`, `Teacher`, `Student`, `Parent`, `Staff`) and granular permissions powered by `spatie/laravel-permission`.
- **API Token Authentication**: Secure token-based authentication via `Laravel Sanctum`.
- **Unified Pagination Engine**: Standard pagination (`page`, `per_page`, `limit`) across all collection/index endpoints, with support for unpaginated queries (`all=true` / `paginate=false`).
- **Full School Management Lifecycle**:
  - Academic Years, Classes, Sections, and Subjects
  - Student enrollment and parent linkage
  - Teacher subject and section assignments
  - Interactive timetables with conflict prevention
  - Daily student & teacher attendance tracking
  - Examinations, grade marks, and percentage calculations
  - Fee structures, invoicing, payment tracking, and balance status
  - School announcements (notices) and events calendar
  - Real-time analytical dashboard and summary reports

---

## 🛠 Tech Stack & Requirements

- **PHP**: ^8.3
- **Framework**: Laravel 12.x
- **Database**: SQLite (default for development/testing), MySQL 8.0+, or PostgreSQL 14+
- **Authentication**: Laravel Sanctum 4.x
- **Permissions**: Spatie Laravel-Permission 8.x
- **Testing**: Pest 4.x & PHPUnit
- **Code Standards**: Laravel Pint

---

## ⚙️ Installation & Quick Start

### 1. Clone the repository & install dependencies
```bash
git clone <repository-url>
cd school_mangement_system
composer install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Ensure your database connection is set in `.env` (SQLite or MySQL):
```env
DB_CONNECTION=sqlite
# or for MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_management
# DB_USERNAME=root
# DB_PASSWORD=
```

For SQLite, create the database file if it doesn't exist:
```bash
touch database/database.sqlite
```

### 3. Run Migrations & Seeders
Populates the database with roles, permissions, admin credentials, and comprehensive demo records:
```bash
php artisan migrate --seed
```

### 4. Start the Development Server
```bash
php artisan serve
```
The API is now accessible at `http://127.0.0.1:8000/api`.

---

## 🔑 Default Demo Credentials

All seeded demo accounts share the default passwords listed below:

| Role | Name | Email | Password |
|---|---|---|---|
| **Admin** | School Admin | `admin@school.com` | `Admin@12345` |
| **Teacher** | Amina Khan | `teacher1@school.test` | `password` |
| **Teacher** | Daniel Smith | `teacher2@school.test` | `password` |
| **Student** | Noah Johnson | `student1@school.test` | `password` |
| **Student** | Sophia Williams | `student2@school.test` | `password` |
| **Parent** | Michael Johnson | `parent1@school.test` | `password` |
| **Parent** | Olivia Brown | `parent2@school.test` | `password` |
| **Staff** | Grace Wilson | `staff1@school.test` | `password` |

---

## 🔐 Authentication & Usage

All protected endpoints require the following headers:
```http
Accept: application/json
Authorization: Bearer <YOUR_ACCESS_TOKEN>
```

### 1. Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@school.com",
  "password": "Admin@12345"
}
```
**Response (200 OK):**
```json
{
  "message": "Login successful.",
  "token": "1|abcdef123456...",
  "user": {
    "id": 1,
    "name": "School Admin",
    "email": "admin@school.com",
    "roles": ["Admin"],
    "permissions": ["..."]
  }
}
```

### 2. Logout
```http
POST /api/logout
Authorization: Bearer <YOUR_ACCESS_TOKEN>
```

---

## 📄 Unified Pagination API

All listing (`index`) endpoints share a consistent pagination structure that works seamlessly with frontend tables, mobile apps, and dropdown selects.

### Query Parameters

| Parameter | Type | Default | Description |
|---|---|---|---|
| `page` | `integer` | `1` | The current page number. |
| `per_page` (or `perPage`, `limit`) | `integer` | `10` | Records per page (capped at 100). |
| `all` or `paginate=false` | `boolean` | `false` | When `all=true` or `paginate=false` or `per_page=all`, returns **all records** without pagination (ideal for dropdowns and selects). |
| `search` | `string` | `null` | Keyword filter on resources supporting search (name, email, code, title, etc.). |

### Standard Paginated Response Example
`GET /api/students?page=1&per_page=10`
```json
{
  "success": true,
  "message": "Students retrieved successfully.",
  "data": [
    {
      "id": 1,
      "name": "Noah Johnson",
      "email": "student1@school.test",
      "phone": "+1555000101",
      "gender": "male",
      "status": "active"
    }
  ],
  "pagination": {
    "total": 50,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "total_pages": 5,
    "last_page": 5,
    "from": 1,
    "to": 10,
    "has_more_pages": true,
    "prev_page_url": null,
    "next_page_url": "http://127.0.0.1:8000/api/students?page=2"
  },
  "current_page": 1,
  "last_page": 5,
  "per_page": 10,
  "total": 50
}
```

### Unpaginated Response Example (Dropdowns / Exports)
`GET /api/classes?all=true`
```json
{
  "success": true,
  "message": "Classes retrieved successfully.",
  "data": [
    { "id": 1, "name": "Grade 1", "status": "active" },
    { "id": 2, "name": "Grade 2", "status": "active" }
  ],
  "pagination": null,
  "total": 2
}
```

---

## 📡 API Endpoints Reference

### 1. Authentication & Profile
| Method | Endpoint | Access / Role | Description |
|---|---|---|---|
| `POST` | `/api/login` | Public | Authenticate user & issue Bearer token |
| `POST` | `/api/logout` | Authenticated | Revoke current access token |
| `GET` | `/api/me` | Authenticated | Get currently authenticated user details |
| `GET` | `/api/my-profile/student` | `Student` | Get logged-in student profile |
| `GET` | `/api/my-profile/my-children` | `Parent` | Get children of logged-in parent |

---

### 2. Academic Management
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/academic-years` | `academic_years.view` | List academic years (paginated) |
| `POST` | `/api/academic-years` | `academic_years.create` | Create academic year |
| `GET` | `/api/academic-years/{id}` | `academic_years.view` | View academic year |
| `PUT` | `/api/academic-years/{id}` | `academic_years.update` | Update academic year |
| `DELETE` | `/api/academic-years/{id}` | `academic_years.delete` | Delete academic year |
| `GET` | `/api/classes` | `classes.view` | List classes (paginated) |
| `POST` | `/api/classes` | `classes.create` | Create class |
| `GET` | `/api/classes/{id}` | `classes.view` | View class |
| `PUT` | `/api/classes/{id}` | `classes.update` | Update class |
| `DELETE` | `/api/classes/{id}` | `classes.delete` | Delete class |
| `GET` | `/api/sections` | `sections.view` | List sections (paginated) |
| `POST` | `/api/sections` | `sections.create` | Create section |
| `GET` | `/api/sections/{id}` | `sections.view` | View section |
| `PUT` | `/api/sections/{id}` | `sections.update` | Update section |
| `DELETE` | `/api/sections/{id}` | `sections.delete` | Delete section |
| `GET` | `/api/subjects` | `subjects.view` | List subjects (paginated) |
| `POST` | `/api/subjects` | `subjects.create` | Create subject |
| `GET` | `/api/subjects/{id}` | `subjects.view` | View subject |
| `PUT` | `/api/subjects/{id}` | `subjects.update` | Update subject |
| `DELETE` | `/api/subjects/{id}` | `subjects.delete` | Delete subject |

---

### 3. People Management
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/students` | `students.view` | List students (paginated, search, status) |
| `POST` | `/api/students` | `students.create` | Create student profile |
| `GET` | `/api/students/{id}` | `students.view` | View student details |
| `PUT` | `/api/students/{id}` | `students.update` | Update student details |
| `DELETE` | `/api/students/{id}` | `students.delete` | Delete student |
| `GET` | `/api/teachers` | `teachers.view` | List teachers (paginated) |
| `POST` | `/api/teachers` | `teachers.create` | Create teacher |
| `GET` | `/api/teachers/{id}` | `teachers.view` | View teacher |
| `PUT` | `/api/teachers/{id}` | `teachers.update` | Update teacher |
| `DELETE` | `/api/teachers/{id}` | `teachers.delete` | Delete teacher |
| `GET` | `/api/staff` | `staff.view` | List non-teaching staff (paginated) |
| `POST` | `/api/staff` | `staff.create` | Create staff member |
| `GET` | `/api/staff/{id}` | `staff.view` | View staff member |
| `PUT` | `/api/staff/{id}` | `staff.update` | Update staff member |
| `DELETE` | `/api/staff/{id}` | `staff.delete` | Delete staff member |
| `GET` | `/api/enrollments` | `enrollments.view` | List student enrollments (paginated) |
| `POST` | `/api/enrollments` | `enrollments.create` | Enroll student in class/section |
| `GET` | `/api/enrollments/{id}` | `enrollments.view` | View enrollment |
| `PUT` | `/api/enrollments/{id}` | `enrollments.update` | Update enrollment status |
| `DELETE` | `/api/enrollments/{id}` | `enrollments.delete` | Delete enrollment |
| `GET` | `/api/teacher-assignments` | `teacher_assignments.view` | List teacher assignments (paginated) |
| `POST` | `/api/teacher-assignments` | `teacher_assignments.create` | Assign teacher to class/section/subject |
| `DELETE` | `/api/teacher-assignments/{id}` | `teacher_assignments.delete` | Remove teacher assignment |

---

### 4. Timetable
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/timetables` | `timetables.view` | List timetables (paginated, filters) |
| `POST` | `/api/timetables` | `timetables.create` | Create timetable slot with collision check |
| `GET` | `/api/timetables/{id}` | `timetables.view` | View timetable slot |
| `PUT` | `/api/timetables/{id}` | `timetables.update` | Update timetable slot |
| `DELETE` | `/api/timetables/{id}` | `timetables.delete` | Delete timetable slot |

---

### 5. Attendance
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/student-attendance` | `student_attendance.view` | List student attendance (paginated, date, class, section) |
| `POST` | `/api/student-attendance` | `student_attendance.create` | Record student attendance |
| `GET` | `/api/teacher-attendance` | `teacher_attendance.view` | List teacher attendance (paginated) |
| `POST` | `/api/teacher-attendance` | `teacher_attendance.create` | Record teacher attendance |
| `GET` | `/api/my-teacher/attendance` | Role: `Teacher` | Teacher's view of student attendance |
| `GET` | `/api/my-teacher/attendance/students` | Role: `Teacher` | List students for teacher's assigned section |
| `POST` | `/api/my-teacher/attendance` | Role: `Teacher` | Bulk save attendance for teacher's section |
| `GET` | `/api/my-student/attendance` | Role: `Student` | Student's own attendance history |
| `GET` | `/api/my-parent/children/{student}/attendance` | Role: `Parent` | Parent's view of child's attendance |

---

### 6. Exams & Results
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/exams` | `exams.view` | List exams (paginated, class, academic year) |
| `POST` | `/api/exams` | `exams.create` | Create exam |
| `GET` | `/api/exams/{id}` | `exams.view` | View exam |
| `PUT` | `/api/exams/{id}` | `exams.update` | Update exam |
| `DELETE` | `/api/exams/{id}` | `exams.delete` | Delete exam |
| `GET` | `/api/results` | `results.view` | List exam results (paginated, student, exam) |
| `POST` | `/api/results` | `results.create` | Add exam result |
| `PUT` | `/api/results/{id}` | `results.update` | Update exam result |
| `DELETE` | `/api/results/{id}` | `results.delete` | Delete exam result |
| `GET` | `/api/my-student/results` | Role: `Student` | Student's own exam results |
| `GET` | `/api/my-parent/children/{student}/results` | Role: `Parent` | Parent's view of child's results |

---

### 7. Fees & Payments
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/fees` | `fees.view` | List fee invoices (paginated, filters) |
| `POST` | `/api/fees` | `fees.create` | Generate fee invoice |
| `GET` | `/api/fees/{id}` | `fees.view` | View fee details |
| `PUT` | `/api/fees/{id}` | `fees.update` | Update fee invoice |
| `DELETE` | `/api/fees/{id}` | `fees.delete` | Delete fee invoice |
| `GET` | `/api/payments` | `payments.view` | List fee payments (paginated) |
| `POST` | `/api/payments` | `payments.create` | Record payment against fee |
| `GET` | `/api/payments/{id}` | `payments.view` | View payment record |
| `DELETE` | `/api/payments/{id}` | `payments.delete` | Delete payment record |
| `GET` | `/api/fee-reports` | `fee_reports.view` | Fee report with total/paid/remaining summaries |
| `GET` | `/api/my-student/fees` | Role: `Student` | Student's own fee records |
| `GET` | `/api/my-student/payments` | Role: `Student` | Student's own payment history |
| `GET` | `/api/my-parent/children/{student}/fees` | Role: `Parent` | Child's fee records |
| `GET` | `/api/my-parent/children/{student}/payments` | Role: `Parent` | Child's payment history |

---

### 8. Notices & Events
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/notices` | `notices.view` | List school notices (paginated, search, status) |
| `POST` | `/api/notices` | `notices.create` | Publish notice |
| `GET` | `/api/notices/{id}` | `notices.view` | View notice |
| `PUT` | `/api/notices/{id}` | `notices.update` | Update notice |
| `DELETE` | `/api/notices/{id}` | `notices.delete` | Delete notice |
| `GET` | `/api/events` | `events.view` | List school calendar events (paginated) |
| `POST` | `/api/events` | `events.create` | Create calendar event |
| `GET` | `/api/events/{id}` | `events.view` | View event |
| `PUT` | `/api/events/{id}` | `events.update` | Update event |
| `DELETE` | `/api/events/{id}` | `events.delete` | Delete event |

---

### 9. Reports
| Method | Endpoint | Permission | Description |
|---|---|---|---|
| `GET` | `/api/reports/students` | `student_reports.view` | Student demographic report (paginated) |
| `GET` | `/api/reports/attendance` | `attendance_reports.view` | Attendance rate summary report (paginated) |
| `GET` | `/api/reports/fees` | `fee_reports.view` | Fee collection summary report (paginated) |
| `GET` | `/api/reports/results` | `result_reports.view` | Exam performance and marks report (paginated) |

---

### 10. Dashboard & Settings
| Method | Endpoint | Permission / Middleware | Description |
|---|---|---|---|
| `GET` | `/api/dashboard` | Role: `Admin` | Overview statistics, student/teacher counts, attendance rates |
| `GET` | `/api/school-settings` | `school_settings.view` | Get school profile, logo, address, and metadata |
| `POST` | `/api/school-settings` | `school_settings.update` | Create school settings |
| `POST` | `/api/school-settings/update` | `school_settings.update` | Update school settings |

---

### 11. User & Role Management
| Method | Endpoint | Permission | Description |
|---|---|---|---|
| `GET` | `/api/users` | `users.view` | List users (paginated, search, role) |
| `POST` | `/api/users` | `users.create` | Create user account with assigned role |
| `GET` | `/api/users/{id}` | `users.view` | View user profile |
| `PUT` | `/api/users/{id}` | `users.update` | Update user details |
| `DELETE` | `/api/users/{id}` | `users.delete` | Delete user account |
| `POST` | `/api/users/{id}/connect-teacher` | `users.update` | Associate user with teacher record |
| `POST` | `/api/users/{id}/connect-student` | `users.update` | Associate user with student record |
| `POST` | `/api/users/{id}/connect-staff` | `users.update` | Associate user with staff record |

---

## 🧪 Testing & Quality Assurance

### Run Test Suite
Run feature and unit tests with Pest:
```bash
vendor/bin/pest
# or via artisan:
php artisan test --compact
```

### Code Formatting
Ensure PHP code adheres to project standards using Laravel Pint:
```bash
vendor/bin/pint --format agent
```

---

## 📄 License

This application is open-sourced software licensed under the [MIT License](LICENSE).
