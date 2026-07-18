# Academix ERP

A modern School Enterprise Resource Planning (ERP) system built with Laravel 12.

## About The Project

Academix ERP is a comprehensive school management platform designed to simplify academic, administrative, financial, and human resources operations within educational institutions.

The application provides a centralized system for managing students, teachers, parents, classes, attendance, grades, report cards, payments, and teacher contracts.

Built with Laravel 12, Tailwind CSS, and modern development practices, this project demonstrates real-world enterprise application architecture.

---

## Key Features

### Authentication & Authorization

* Laravel Breeze Authentication
* Role & Permission Management with Spatie Laravel Permission
* Secure Access Control
* Multi-Role Support

Roles:

* Administrator
* Teacher
* Student
* Parent

---

### Student Management

* Student Registration
* Student Profiles
* Parent Association
* Class Assignment
* Academic Tracking

---

### Teacher Management

* Teacher Profiles
* Subject Assignments
* Teacher Contracts
* Qualifications Management
* Employment Tracking

---

### Academic Management

* Academic Years
* Terms / Trimesters
* Classes
* Subjects
* Timetables
* Teacher-Class Assignments

---

### Attendance Management

* Daily Attendance Tracking
* Present / Absent / Late Records
* Attendance History
* Attendance Statistics

---

### Grade Management

* Homework Scores
* Tests
* Examinations
* Automatic Average Calculation
* Student Ranking

---

### Report Cards

* PDF Report Card Generation
* Academic Performance Summary
* Ranking Information
* Downloadable Reports

---

### Financial Management

* Fee Type Management
* Student Payments
* Payment Tracking
* Receipt Generation
* QR Code Verification

---

### Communication

* SMS Notifications via Twilio
* Parent Alerts
* Attendance Notifications
* Payment Notifications
* School Announcements

---

### Reports & Analytics

* Dashboard Overview
* Attendance Statistics
* Financial Statistics
* Student Statistics
* Interactive Charts with Chart.js

---

### Data Export

Export school data to Excel using Laravel Excel:

* Students
* Grades
* Attendance
* Payments

---

## Technology Stack

### Backend

* Laravel 12
* PHP 8+
* MySQL
* Eloquent ORM

### Frontend

* Blade
* Tailwind CSS
* Bootstrap Icons
* Chart.js
* Flatpickr

### Packages

* Laravel Breeze
* Spatie Laravel Permission
* Barryvdh DomPDF
* Laravel Excel
* Simple QRCode
* Twilio SDK
* Bondage

---

## Database Modules

### User Management

* Users
* Students
* Teachers
* Parents

### Academic

* Academic Years
* Terms
* Classes
* Subjects
* Timetables
* Teacher Assignments

### Evaluation

* Grades
* Report Cards

### Attendance

* Attendance Records

### Finance

* Fee Types
* Payments

### Human Resources

* Teacher Contracts

---

## UI Design

### Color Palette

Primary Color

```text
Blue 600 (#2563EB)
```

Secondary Color

```text
Emerald 500 (#10B981)
```

Framework

```text
Tailwind CSS
Bootstrap Icons
```

---

## Installation

```bash
git clone [https://github.com/Cyrus-CS/academix-erp.git

cd academix-erp

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

npm install

npm run build

php artisan serve
```

---

## Project Status

🚧 In Development

The project is actively under development and new modules are being added progressively.

---

## Planned Features

* Parent Portal
* Student Portal
* Mobile Money Integration
* REST API
* Email Notifications
* Multi-School Support
* Mobile Application

---

## Author

Eben-ezer Sissou

Laravel Developer

---

## License

MIT License
