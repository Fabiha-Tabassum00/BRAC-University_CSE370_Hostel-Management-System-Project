-- Creating the 'room' table first
DROP TABLE IF EXISTS room;
CREATE TABLE IF NOT EXISTS room (
    room_number INT PRIMARY KEY,
    capacity INT,
    `single` INT,  -- Backticks are added here
    `double` INT   -- Backticks are added here
);
INSERT INTO room (room_number, capacity, `single`, `double`) VALUES
(101, 2, 1, 1),
(102, 2, 1, 1),
(103, 2, 1, 1),
(104, 2, 1, 1),
(105, 2, 1, 1),
(106, 2, 1, 1),
(107, 2, 1, 1),
(108, 2, 1, 1),
(109, 2, 1, 1),
(110, 2, 1, 1) -- Added a closing parenthesis here to complete the INSERT statement
;



-- Creating the 'student' table with the correct foreign key reference
CREATE TABLE student (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    phone_number VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    room_id INT,
    department VARCHAR(50),
    semester INT,
    Password VARCHAR(100) NOT NULL
    address TEXT,
    FOREIGN KEY (room_id) REFERENCES room(room_number)
);
INSERT INTO student (student_id, name, phone_number, email, room_id, department, semester,password,address) VALUES
('23301687', 'efti', '01712666554','efti@g.bracu.ac.bd', '23', 'cse, 7', '1221''banasree');

-- Creating the 'fee' table with foreign key reference to 'student'
CREATE TABLE fee (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(10, 2),
    fee_status VARCHAR(50),
    due_date DATE,
    student_id INT,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
);
insert into fee (transaction_id, amount, fee_status, due_date, student_id) values
(1, 5000.00, 'Paid', '2023-10-01', 23301687),
(2, 3000.00, 'Unpaid', '2023-11-01', 23301687),
(3, 7000.00, 'Paid', '2023-12-01', 23301687);

-- Creating the 'staff' table
CREATE TABLE staff (
    staff_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    staff_type VARCHAR(50),
    contact VARCHAR(50)
);
insert into staff (staff_id, name, staff_type, contact) values
(1, 'John Doe', 'Warden', '01712345678'),
(2, 'Jane Smith', 'Security', '01787654321'),
(3, 'Alice Johnson', 'Cleaner', '01711223344');
-- Creating the 'visitor' table
CREATE TABLE visitor (
    visitor_id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE,
    name VARCHAR(100),
    purpose TEXT,
    student_id INT,
    FOREIGN KEY (student_id) REFERENCES student(student_id)
);
insert into visitor (visitor_id, date, name, purpose, student_id) values
(1, '2023-10-01', 'Mark Twain', 'Visiting Efti', 23301687),
(2, '2023-10-02', 'Samuel Clemens', 'Visiting Efti', 23301687),
(3, '2023-10-03', 'Ernest Hemingway', 'Visiting Efti', 23301687);
-- Creating the 'complaint' table
CREATE TABLE complaint (
    complaint_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    staff_id INT,
    complaint_status VARCHAR(50),
    complaint_type TEXT,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);
insert into complaint (complaint_id, student_id, staff_id, complaint_status, complaint_type) values
(1, 23301687, 1, 'Resolved', 'Noise issue'),
(2, 23301687, 2, 'Pending', 'Water supply issue'),
(3, 23301687, 3, 'Resolved', 'Cleaning issue');
-- Creating the 'leave_request' table
CREATE TABLE leave_request (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    staff_id INT,
    start_date DATE,
    end_date DATE,
    request_status VARCHAR(50),
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);
insert into leave_request (request_id, student_id, staff_id, start_date, end_date, request_status) values
(1, 23301687, 1, '2023-10-01', '2023-10-05', 'Approved'),
(2, 23301687, 2, '2023-10-06', '2023-10-10', 'Pending'),
(3, 23301687, 3, '2023-10-11', '2023-10-15', 'Rejected');
-- Creating the 'booking' table
CREATE TABLE booking (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    start_date DATE,
    end_date DATE,
    student_id INT,
    room_number INT,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (room_number) REFERENCES room(room_number)
);
insert into booking (booking_id, start_date, end_date, student_id, room_number) values
(1, '2023-10-01', '2023-10-05', 23301687, 101),
(2, '2023-10-06', '2023-10-10', 23301687, 102),
(3, '2023-10-11', '2023-10-15', 23301687, 103);
-- Creating the 'maintenance_req' table
CREATE TABLE maintenance_req (
    req_id INT PRIMARY KEY AUTO_INCREMENT,
    room_number INT,
    staff_id INT,
    request_date DATE,
    FOREIGN KEY (room_number) REFERENCES room(room_number),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);
insert into maintenance_req (req_id, room_number, staff_id, request_date) values
(1, 101, 1, '2023-10-01'),
(2, 102, 2, '2023-10-02'),
(3, 103, 3, '2023-10-03');
-- Creating the 'student_report' table
CREATE TABLE student_report (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report TEXT,
    student_id INT,
    staff_id INT,
    FOREIGN KEY (student_id) REFERENCES student(student_id),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);
insert into student_report (report_id, report, student_id, staff_id) values
(1, 'Student is not following the rules', 23301687, 1),
(2, 'Student is not attending classes', 23301687, 2),
(3, 'Student is not maintaining cleanliness', 23301687, 3);
