-- Creating the 'admin' table first since it is referenced by other tables
CREATE TABLE Admin (
    admin_id INT PRIMARY KEY,
    admin_name VARCHAR(100),
    admin_contact VARCHAR(50),
    admin_email VARCHAR(100)
);

-- Creating the 'user' table
CREATE TABLE User (
    st_id INT PRIMARY KEY,
    st_name VARCHAR(100),
    st_contact VARCHAR(50),
    dept VARCHAR(100),
    semester INT,
    email VARCHAR(100),
    password VARCHAR(100),
    address TEXT
);

-- Creating the 'room' table
CREATE TABLE Room (
    room_number INT PRIMARY KEY,
    status VARCHAR(50),
    fee DECIMAL(10, 2),
    single INT,
    shared INT,
    admin_id INT,
    available_spots INT,
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id) -- Admin's reference here
);

-- Creating the 'visitor' table
CREATE TABLE Visitor (
    visitor_id INT PRIMARY KEY,
    name VARCHAR(100),
    date DATE,
    purpose TEXT,
    st_id INT,
    admin_id INT,
    FOREIGN KEY (st_id) REFERENCES User(st_id),
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id) -- Admin's reference here
);

-- Creating the 'bill' table
CREATE TABLE Bill (
    transaction_id INT PRIMARY KEY,
    amount DECIMAL(10, 2),
    bill_date DATE,
    st_id INT,
    admin_id INT,
    FOREIGN KEY (st_id) REFERENCES User(st_id),
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);

-- Creating the 'leave_request' table
CREATE TABLE Leave_Request (
    request_id INT PRIMARY KEY,
    admin_id INT,
    start_date DATE,
    end_date DATE,
    reason TEXT,
    st_id INT,
    FOREIGN KEY (st_id) REFERENCES User(st_id),
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);

-- Creating the 'maintenance_request' table
CREATE TABLE Maintenance_Req (
    req_id INT PRIMARY KEY,
    room_number INT,
    admin_id INT,
    request_date DATE,
    FOREIGN KEY (room_number) REFERENCES Room(room_number),
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);


-- Insert sample data for 'user' table
-- Creating the 'User' table


-- Inserting data into the 'User' table
INSERT INTO User (st_id, st_name, st_contact, dept, semester, email, password, address) 
VALUES (23301687, 'efti', '01712666554', 'CSE, 7', 7, 'efti@g.bracu.ac.bd', 'password123', 'Banasree');


-- Insert sample data for 'room' table
INSERT INTO Room (room_number, status, fee, single, shared, admin_id) 
VALUES (102, 'Available', 2500.00, 1, 1, 1);


-- Insert sample data for 'visitor' table
INSERT INTO Visitor (visitor_id, name, date, purpose, st_id, admin_id) VALUES
(1, 'Mark Twain', '2023-10-01', 'Visiting Efti', 23301687, 1);

-- Insert sample data for 'bill' table
INSERT INTO Bill (transaction_id, amount, bill_date, st_id, admin_id) VALUES
(1, 5000.00, '2023-10-01', 23301687, 1);

-- Insert sample data for 'leave_request' table
INSERT INTO Leave_Request (request_id, admin_id, start_date, end_date, reason, st_id) VALUES
(1, 1, '2023-10-01', '2023-10-05', 'Personal', 23301687);

-- Insert sample data for 'maintenance_request' table
INSERT INTO Maintenance_Req (req_id, room_number, admin_id, request_date) VALUES
(1, 101, 1, '2023-10-01');

-- Insert sample data for 'admin' table
INSERT INTO Admin (admin_id, admin_name, admin_contact, admin_email) VALUES
(1, 'John Doe', '01712345678', 'admin@hostel.com');

INSERT INTO Room (room_number, status, fee, single, shared, admin_id) VALUES
(101, 'Occupied', 2000.00, 1, 1, 1);