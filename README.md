# 🎓 Educational Performance Tracking System

This is a web-based academic performance management system designed for educational institutions. It enables administrators, lecturers, and students to interact with various modules related to academic records, attendance, activities, and more.

---

## 🚀 Key Features

### 👨‍🎓 Student Dashboard
- View grades, GPA/CGPA, and retake courses
- See attendance history
- View and join activities
- Submit complaints

### 👩‍🏫 Lecturer Dashboard
- Submit student marks with automated grade calculation
- Auto-fill student and course details
- View student performance for their taught course units
- View student participation in activities

### 👨‍💼 Admin Dashboard
- View all student performance summaries and details
- View individual student trends via graphs
- Filter performance data by year, lecturer, or course unit
- View and respond to complaints
- Manage users (add, edit, delete roles)
- Set system-wide parameters (academic year, pass mark, term)

---

## 🧠 System Modules

| Module                 | Description |
|------------------------|-------------|
| **Student Grades**     | Dynamic mark entry, GPA/CGPA calculation, grade breakdown |
| **Attendance**         | Lecturers mark attendance, students view history |
| **Activities**         | Admin/lecturer populates activities, students choose to join |
| **Complaints**         | Students submit, admins/lecturers manage |
| **User Management**    | Admin adds/edit roles (students, lecturers) |
| **System Settings**    | Admin sets academic year, term, pass mark, etc. |
| **Performance Graphs** | Line graph comparing marks across time, by student or unit |

---

## 🧰 Tech Stack

- **Frontend:** HTML + CSS
- **Backend:** PHP (Vanilla)
- **Database:** MySQL
- **Charts:** Chart.js
- **Hosting (Dev):** XAMPP (localhost)

---

## 🗂️ Project Structure Overview

```
edu-performance/
├── Images/                    # UI images used across pages
├── style.css                 # Shared stylesheet
├── db_connect.php            # DB connection file
├── *.html                    # Frontend pages
├── *.php                     # Backend logic (view, insert, update)
├── README.md                 # This file
```

---

## 🧪 Local Installation Instructions

1. **Clone the Repo**
```bash
git clone https://github.com/MarkjoeTumukunde/edu-performance.git
cd edu-performance
```

2. **Set Up the Database**
- Create a database named `edu_performance`
- Import the SQL dump file (from your local/export folder)

3. **Configure DB Connection**
Edit `db_connect.php`:
```php
$conn = new mysqli("localhost", "root", "", "edu_performance");
```

4. **Run Locally**
- Use XAMPP or similar
- Visit: `http://localhost/edu-performance/`

---

## 📊 Sample Pages & Screens

- `student_dashboard.html` — Grades, Attendance, Complaints
- `lecturer_dashboard.html` — Add marks, View performances
- `admin_dashboard.html` — Access everything

---

## 📦 Deployment (Upcoming Guide)

- Upload files to your cPanel or free host (e.g. InfinityFree)
- Create/import MySQL DB
- Update credentials in `db_connect.php`
- Done 🎉

---

## 🙌 Credits

Built by **Markjoe Tumukunde** with support from **ChatGPT** 🤖  
2025 — Final Year Computer Science Project

---

## 📬 Contact

Have questions or want to contribute?
Feel free to reach out via [GitHub Issues](https://github.com/MarkjoeTumukunde/edu-performance/issues)

---

> "Empowering schools with smart digital tools for better academic decisions."
> — Markjoe Tumukunde & Abigaba Roland,  Developers