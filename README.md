# Desktop_&_Web_Project
This Project Live  - https://web347.free.nf/

- Admin: admin@cyberlearn.com / password
- Student: emon@cyberlearn.com / password

# CyberLearn - Learning Management System

## Overview

CyberLearn is a comprehensive online learning management system web application that provides educational content delivery and user management functionality. Built with HTML, CSS, JavaScript, and PHP with JSON for project database.

## Project Architecture

### Directory Structure

```
/
├── admin/                 # Admin dashboard pages
│   └── dashboard.php      # Course management interface
├── api/                   # REST API endpoints
│   ├── courses.php        # Course CRUD operations
│   └── enrollments.php    # Student enrollment handling
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet (dark theme)
│   └── js/
│       ├── admin.js       # Admin dashboard JavaScript
│       └── student.js     # Student dashboard JavaScript
├── data/                  # JSON database files
│   ├── users.json         # User accounts
│   ├── courses.json       # Course content
│   └── enrollments.json   # Student enrollments
├── includes/              # PHP includes/helpers
│   ├── config.php         # Configuration and utilities
│   ├── auth.php           # Authentication functions
│   └── courses.php        # Course management functions
├── student/               # Student dashboard pages
│   ├── dashboard.php      # Course browsing
│   ├── my-courses.php     # Enrolled courses
│   └── course.php         # Course detail view
├── index.php              # Login page (entry point)
├── register.php           # Registration page
└── logout.php             # Logout handler
```

### Technology Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP 8.3
- **Database**: JSON files
  
### Design System

- **Colors**:
  - Primary: #2563EB (education blue)
  - Secondary: #059669 (success green)
  - Background: #1a1f2e (dark slate)
  - Card Background: #252b3b
  - Text: #e2e8f0 (light)
  - Accent/Cyan: #22d3ee
  - Category Colors: Orange (#f97316), Purple (#a855f7), Teal (#14b8a6), Green (#22c55e)

## User Roles

1. **Admin**: Can create, edit, and delete courses
2. **Student**: Can browse courses, enroll, and track progress

## Default Test Accounts


