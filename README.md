# BRACUBNB — Student Hostel Management System

A web-based hostel management system built for the **CSE370 (Database Systems)** course. It manages student accommodation end to end — room booking, leave, visitor, and maintenance requests, and attendance — backed by a structured relational database design with ER/EER and schema diagrams.

## Features

**Student**
- Explore room availability and book a room
- Submit leave requests
- Submit visitor requests
- Submit maintenance requests
- Cancel a booking
- View personal account and attendance records

**Admin**
- Approve or deny student requests
- Manage room bookings and student records
- Filter student details (e.g. search by department such as CSE or EEE)
- Manage attendance

## Tech Stack

**Frontend:** HTML, Bootstrap
**Backend:** PHP, MySQL

## Database

The system is designed around ER/EER and schema diagrams for efficient data retrieval. The database is provided as `hostel_management.sql`.

## Running Locally

1. Install a local PHP/MySQL stack such as **XAMPP** or **WAMP**.
2. Copy this project into the server's web root (e.g. `htdocs/` for XAMPP).
3. In **phpMyAdmin**, create a database named `hostel_management` and import `hostel_management.sql`.
4. Check the database settings in `db.php` (defaults match a standard XAMPP setup: host `localhost`, user `root`, empty password).
5. Start Apache and MySQL, then open `http://localhost/Hostel_Management_System/` in a browser.

## Team

Built as a group project for CSE370:

- Ahmed Mustyaeen
- Fabiha Tabassum Poroma
- Md Akib
- Maliha Momtaz Rayeta

*Each member contributed to both frontend and backend.*