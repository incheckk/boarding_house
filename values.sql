-- tenant_status, room_status (kept)
CREATE TABLE tenant_status (
    tstat_id INT AUTO_INCREMENT PRIMARY KEY,
    tstat_desc VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE room_status (
    rstat_id INT AUTO_INCREMENT PRIMARY KEY,
    rstat_desc VARCHAR(50) NOT NULL UNIQUE
);

-- rooms
CREATE TABLE room (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    room_size VARCHAR(50),
    room_rate DECIMAL(10,2) NOT NULL,
    rstat_id INT NOT NULL,
    FOREIGN KEY (rstat_id) REFERENCES room_status(rstat_id) ON UPDATE CASCADE,
    INDEX (room_rate)
);

-- tenants
CREATE TABLE tenant (
    tenant_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    number VARCHAR(20) NOT NULL,
    emergency_number VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    tstat_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tstat_id) REFERENCES tenant_status(tstat_id) ON UPDATE CASCADE
);

-- mapping tenant <-> room (current occupant or historical)
CREATE TABLE roomtenant (
    room_tenant_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE,
    role_in_room VARCHAR(50) DEFAULT 'Member',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES room(room_id) ON DELETE CASCADE
);

-- reservation status + reservations
CREATE TABLE tenant_reservation_status (
    restat_id INT AUTO_INCREMENT PRIMARY KEY,
    restat_desc VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE reservation (
    reservation_id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_date DATE NOT NULL DEFAULT CURRENT_DATE,
    restat_id INT NOT NULL,
    tenant_id INT NOT NULL,
    room_id INT NOT NULL,
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restat_id) REFERENCES tenant_reservation_status(restat_id) ON UPDATE CASCADE,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES room(room_id) ON DELETE CASCADE
);

-- amenities
CREATE TABLE amenities (
    amen_id INT AUTO_INCREMENT PRIMARY KEY,
    amen_name VARCHAR(50) NOT NULL UNIQUE,
    amen_desc VARCHAR(255)
);
CREATE TABLE roomamenities (
    room_id INT NOT NULL,
    amen_id INT NOT NULL,
    PRIMARY KEY (room_id, amen_id),
    FOREIGN KEY (room_id) REFERENCES room(room_id) ON DELETE CASCADE,
    FOREIGN KEY (amen_id) REFERENCES amenities(amen_id) ON DELETE CASCADE
);

-- payment status
CREATE TABLE payment_status (
    pstat_id INT AUTO_INCREMENT PRIMARY KEY,
    pstat_desc VARCHAR(50) NOT NULL UNIQUE
);

-- billing (one per invoice)
CREATE TABLE billing (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    room_id INT NOT NULL,
    rent_amount DECIMAL(10,2) NOT NULL,
    utilities_amount DECIMAL(10,2) DEFAULT 0,
    other_charges DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) GENERATED ALWAYS AS (rent_amount + utilities_amount + other_charges) VIRTUAL,
    payment_date DATE,
    due_date DATE NOT NULL,
    remarks VARCHAR(255),
    pstat_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES room(room_id) ON DELETE CASCADE,
    FOREIGN KEY (pstat_id) REFERENCES payment_status(pstat_id) ON UPDATE CASCADE,
    INDEX (due_date),
    INDEX (pstat_id)
);

-- payment history/audit (for cash receipts)
CREATE TABLE payment_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    received_amount DECIMAL(10,2) NOT NULL,
    received_by VARCHAR(100),
    received_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes VARCHAR(255),
    FOREIGN KEY (payment_id) REFERENCES billing(payment_id) ON DELETE CASCADE
);

-- utilities (monthly reading records if desired)
CREATE TABLE utilities (
    util_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    month_year VARCHAR(7) NOT NULL, -- format YYYY-MM
    electricity DECIMAL(10,2) DEFAULT 0,
    water DECIMAL(10,2) DEFAULT 0,
    internet DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES room(room_id) ON DELETE CASCADE,
    UNIQUE (room_id, month_year)
);

-- visitors
CREATE TABLE visitorlog (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    visitor_name VARCHAR(100) NOT NULL,
    visit_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    purpose VARCHAR(255),
    tenant_id INT NOT NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE CASCADE
);

-- users (admins) with role constraint
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('Owner','Manager','Staff') NOT NULL,
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- notifications log (what we've sent)
CREATE TABLE notifications_log (
    notif_id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT,
    channel ENUM('email','sms','whatsapp') NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent','failed') DEFAULT 'sent',
    FOREIGN KEY (tenant_id) REFERENCES tenant(tenant_id) ON DELETE SET NULL
);

-- blog posts
CREATE TABLE blog (
    blog_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    image VARCHAR(255) -- optional, store image filename/path
);

-- small indexes for performance
CREATE INDEX idx_room_status ON room(rstat_id);
CREATE INDEX idx_tenant_status ON tenant(tstat_id);


INSERT INTO room_status (rstat_id, rstat_desc)
VALUES 
(1, 'Available'),
(2, 'Occupied'),
(3, 'Reserved');

INSERT INTO room (room_number, room_size, room_rate, rstat_id)
VALUES
('101', 'Single Bed', 1800.00, 1),
('102', 'Single Bed', 1800.00, 1),
('103', 'Single Bed', 1800.00, 1),
('104', 'Single Bed', 1800.00, 1),
('105', 'Single Bed', 1800.00, 1),
('106', 'Single Bed', 1800.00, 1),
('107', 'Single Bed', 1800.00, 1),
('108', 'Single Bed', 1800.00, 1),
('109', 'Single Bed', 1800.00, 1),
('110', 'Single Bed', 1800.00, 1),
('111', 'Single Bed', 1800.00, 1);

INSERT INTO room (room_number, room_size, room_rate, rstat_id)
VALUES
('201', 'Double Bed', 2000.00, 1),
('202', 'Double Bed', 2000.00, 1);

INSERT INTO room (room_number, room_size, room_rate, rstat_id)
VALUES
('301', 'Bunk Bed', 2200.00, 1),
('302', 'Bunk Bed', 2200.00, 1);