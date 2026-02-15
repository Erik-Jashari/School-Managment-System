# School Management System

A full-stack School Management System built with PHP, MySQL, HTML, CSS, and JavaScript. The platform serves as a Moodle-like portal for middle schools, allowing administrators to manage users, groups, lessons, and assignments, while students can view their dashboard, attendance, grades, and profile.

**Status:** Full-stack application — frontend and backend fully implemented.

**Built with:** PHP, MySQL, HTML, CSS, JavaScript

---

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- PHP 7.4+
- A modern web browser

## Quick Start

1. Clone or copy the project into your XAMPP `htdocs` folder (e.g. `htdocs/School-Managment-System`).
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin and run `database/schema.sql` to create the database and tables.
4. Optionally run `database/seed.sql` to populate test data (1 admin, 10 students, groups, lessons, etc.).
5. Visit `http://localhost/School-Managment-System/` in your browser.

### Default Test Credentials (from seed data)

| Role    | Email              | Password   |
|---------|--------------------|------------|
| Admin   | admin@school.com   | password   |
| Student | ardi@school.com    | password   |

---

## Project Structure

```
School-Managment-System/
├── Index.html              # Landing page with hero & features
├── About.html              # About the school
├── Login.php               # Login page (email/password auth)
├── Contact.php             # Contact form (saves to database)
├── Dashboard.php           # Group dashboard (filtered by logged-in user)
├── Student-Profile.php     # Student profile & stats
│
├── admin/                  # Admin panel (requires Admin role)
│   ├── index.php           # Admin home with stats & navigation
│   ├── adminDashboard.php  # Analytics: roster, grades, attendance
│   ├── users.php           # User listing (CRUD)
│   ├── addUser.php         # Create new user (with optional group assignment)
│   ├── edit.php            # Edit user (with group assignment)
│   ├── delete.php          # Generic delete handler
│   ├── contactMessages.php # Manage contact form messages
│   ├── reviews.php         # Manage student reviews
│   ├── grades.php          # Manage student grades
│   ├── lessons.php         # Manage student lessons
│   ├── subjects.php        # Manage student subjects
│   ├── submissions.php     # Manage student submissions
│   ├── schedules.php       # Manage student schedules
│   ├── groups.php          # Manage student groups
│   ├── assignments.php     # Manage student assignments
│   └── css/                # Admin-specific styles
│
├── config/
│   ├── database.php        # MySQL connection (mysqli)
│   └── test-connection.php # DB connection test utility
│
├── includes/
│   ├── auth.php            # Auth library (login, logout, register, guards)
│   ├── checkAuth.php       # AJAX endpoint for session status
│   └── logout.php          # Logout endpoint
│
├── database/
│   ├── schema.sql          # Full database schema (11 tables)
│   └── seed.sql            # Sample data for development
│
├── CSS/                    # Page-level stylesheets + Global.css
├── JS/                     # Client-side scripts
│   ├── HeaderFooter.js     # Dynamic navbar & footer with auth-aware links
│   ├── Auth.js             # Client-side auth guard & session keep-alive
│   ├── Dashboard.js        # Dashboard interactions
│   └── ...                 # Other page scripts
│
└── Images/                 # Static assets (logos, photos)
```

---

## Features

### Public Pages
- **Landing page** — Hero section, feature highlights, background slider
- **About** — School information
- **Contact** — Form that saves messages to the database with validation

### Authentication & Authorization
- Server-side login with bcrypt password hashing
- Role-based access control (Admin / Student)
- Session management with 1-hour lifetime
- Page protection: `requireLogin()`, `requireAdmin()`, `requireStudent()`
- Client-side auth guard with session keep-alive and back-button handling
- Dynamic navbar that adapts to login state (Profile/Logout vs Login)

### Student Pages (require login)
- **Dashboard** — Shows only the groups the student belongs to, with roster and today's attendance
- **Student Profile** — Avatar, stats (submissions, average grade, attendance rate, upcoming assignments), recent submissions, group/subject badges

### Admin Panel (requires Admin role)
- **Admin Home** — Overview stat cards (users, students, messages, reviews) with navigation to all management sections
- **Analytics Dashboard** — Per-group student roster with attendance, grades (letter grades), missing work, and at-risk status
- **User Management** — Full CRUD for users with optional group/class assignment on create and edit
- **Contact Messages** — View, mark read/unread, delete incoming messages
- **Reviews** — View and manage student lesson reviews with star ratings

---

## Database

The system uses **11 tables** in a MySQL database called `school_management_system`:

| Table              | Purpose                                      |
|--------------------|----------------------------------------------|
| Users              | Admin and Student accounts (bcrypt passwords) |
| Groups             | Class groups (e.g. Klasa 10-A)               |
| Subjects           | Academic subjects                            |
| Student_Groups     | Many-to-many: students ↔ groups              |
| Lessons            | Teaching materials per group/subject          |
| Documents          | Uploaded files and documents                  |
| Schedules          | Weekly timetable (day, time, classroom)       |
| Assignments        | Homework/tasks with due dates                 |
| Submissions        | Student submissions with grades and status    |
| Attendance         | Daily attendance records (Present/Absent)     |
| Reviews            | Student ratings and comments on lessons       |
| Contact_Messages   | Messages from the contact form                |

### Database Setup

1. Run `database/schema.sql` to create the database and all tables.
2. Run `database/seed.sql` for test data (optional — development only).
