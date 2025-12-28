# Student Management System - PHP Implementation

## Overview
This is a complete PHP implementation of the student management system with separate interfaces for students and teachers/admins, featuring unified login with role-based routing. **This version uses demo data (no database required) for quick testing and development.**

## Features

### Student Side
- **Unified Login**: Students login and are routed to `home.php`
- **Home/Notices Page**: Welcome message, attendance/grade stats, and notices list
- **Attendance Page**: View subject-wise attendance with overall summary (read-only)
- **Results Page**: View examination results with exam types (read-only)
- **Profile Page**: Edit personal details (name, email, phone, address, DOB, guardian info) but not roll number or program

### Navigation
- Attendance
- Results
- Notices
- Profile

## Installation

### Prerequisites
- PHP 7.4 or higher
- Apache/Nginx web server
- XAMPP/WAMP/MAMP (for local development)
- **No database required** - uses demo data

### Setup
1. Place all PHP files in your web server directory:
   - XAMPP: `htdocs/student-management/`
   - WAMP: `www/student-management/`
   - MAMP: `htdocs/student-management/`

2. Access via browser:
   ```
   http://localhost/student-management/php/login.php
   ```

### File Structure
```
/php/
├── config.php              # Demo data configuration (no database)
├── login.php               # Login page (entry point)
├── home.php                # Student notices page
├── attendance.php          # Student attendance page
├── results.php             # Student results page
├── profile.php             # Student profile page
├── student_nav.php         # Navigation component
├── logout.php              # Logout handler
└── README.md               # This file
```

## Demo Credentials

### Student Login
- **Username**: `student`
- **Password**: `password`
- Routes to: `home.php`

### Teacher Login
- **Username**: `teacher`
- **Password**: `password`
- Routes to: `admin_dashboard.php` (to be implemented)

### Admin Login
- **Username**: `admin`
- **Password**: `password`
- Routes to: `admin_dashboard.php` (to be implemented)

## Demo Data

All data is stored in `config.php` as PHP arrays:

### Included Data
- 1 sample student (John Doe, Roll No: CS2024001)
- 6 subjects (Data Structures, Algorithms, Database Systems, Web Development, Computer Networks, Operating Systems)
- Complete attendance records with varying percentages
- 6 examination results (Mid-term and Final exams)
- 5 recent notices

### Data Persistence
- Profile changes are stored in PHP sessions
- Data resets when you logout or session expires
- Perfect for testing and demonstration purposes

## Page Details

### Login Page (`login.php`)
- Unified login for all users
- Role-based routing:
  - Students → `home.php`
  - Teachers/Admins → `admin_dashboard.php`
- Session management

### Home/Notices Page (`home.php`)
- Welcome message with student name
- Overall attendance percentage card (86.5%)
- Overall grade percentage card (83.3%)
- List of 5 recent notices

### Attendance Page (`attendance.php`)
- Overall attendance summary (86.5%)
- Subject-wise attendance table
- Columns: Subject, Code, Classes Attended, Total Classes, Percentage, Status
- Mobile responsive (shows only Subject, Code, Percentage on small screens)
- Color-coded status indicators (Excellent/Good/Warning/Critical)

### Results Page (`results.php`)
- Average percentage card (83.3%)
- Subject-wise results table
- Columns: Subject, Code, Exam Type, Marks Obtained, Full Marks, Percentage, Grade, Status
- Mobile responsive (shows only Subject, Exam Type, Percentage on small screens)

### Profile Page (`profile.php`)
- Read-only fields: Roll Number, Batch, Program, Enrollment Date
- Editable fields: Name, Email, Phone, Address, DOB, Guardian Name, Guardian Phone
- Form validation
- Success/error messages
- Changes persist in session

## Mobile Responsiveness

### Attendance Table (Mobile View)
Shows only:
- Subject
- Code
- Percentage

### Results Table (Mobile View)
Shows only:
- Subject
- Exam Type
- Percentage

## Security Features
- Session-based authentication
- Role-based access control
- XSS protection using `htmlspecialchars()`
- Form validation
- Demo data (not suitable for production with real data)

## Customization

### Changing Demo Data
Edit the arrays in `config.php`:
- `$demo_users` - User credentials
- `$demo_student` - Student information
- `$demo_subjects` - Subject list
- `$demo_attendance` - Attendance records
- `$demo_results` - Examination results
- `$demo_notices` - Notice list

### Changing Colors/Styles
The application uses Tailwind CSS via CDN. Modify the Tailwind classes in the HTML to change styles.

## Notes
- All data is stored in PHP arrays (no database)
- Session data persists across pages within the same session
- Profile updates are saved in session only
- Mobile menu toggle is implemented with vanilla JavaScript
- Perfect for frontend development and testing

## Converting to Database Version
To convert this to use a real database:
1. Replace demo data arrays in `config.php` with database connection
2. Update queries in each page to use `mysqli_query()` or PDO
3. Implement password hashing with `password_hash()` and `password_verify()`
4. Add proper SQL injection prevention

## Future Enhancements (Not Implemented)
- Teacher/Admin dashboard pages
- Attendance marking interface for teachers
- Grade entry interface for teachers
- Notice management (CRUD) for teachers
- Database integration
- Advanced search and filtering
- PDF export for reports
- Email notifications
- Password reset functionality

## Troubleshooting

### Session Issues
- Check if session directory is writable
- Verify `session_start()` is called in config.php

### Login Not Working
- Verify you're using correct credentials (student/password)
- Check browser console for errors
- Ensure PHP session is working

### Profile Updates Not Saving
- Profile changes are session-based and will reset on logout
- This is expected behavior for demo data

## Support
For issues and questions, please refer to the documentation or contact the development team.