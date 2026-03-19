CREATE DATABASE IF NOT EXISTS medicine_inventory;
USE medicine_inventory;

CREATE TABLE IF NOT EXISTS users (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    email     VARCHAR(100) NOT NULL,
    password  VARCHAR(100) NOT NULL,
    is_active INT DEFAULT 1
);

CREATE TABLE IF NOT EXISTS suppliers (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(100),
    phone   VARCHAR(50),
    address TEXT
);

CREATE TABLE IF NOT EXISTS medicines (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    category    VARCHAR(50) NOT NULL,
    batch_no    VARCHAR(50) NOT NULL,
    quantity    INT DEFAULT 0,
    reorder_level INT DEFAULT 20,
    expiry_date DATE,
    supplier_id INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stock_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    medicine_id INT NOT NULL,
    change_qty  INT NOT NULL,
    action      VARCHAR(20) NOT NULL,
    date        DATE NOT NULL
);

-- Default login user
INSERT INTO users (email, password, is_active)
VALUES ('apotheca@gov.in', '12345', 1);

-- Sample suppliers
INSERT INTO suppliers (name, email, phone, address) VALUES
('ABC Pharma', 'abc@pharma.com', '9876543210', 'Mumbai, India'),
('MedLife Distributors', 'info@medlife.com', '9123456789', 'Delhi, India');

-- Sample medicines
INSERT INTO medicines (name, category, batch_no, quantity, reorder_level, expiry_date, supplier_id) VALUES
('Paracetamol 500mg', 'tablet', 'B-1001', 100, 20, '2027-06-15', 1),
('Amoxicillin 250mg', 'tablet', 'B-1002', 8, 15, '2026-02-10', 1),
('Benadryl Cough Syrup', 'syrup', 'C-2001', 45, 20, '2027-12-01', 2),
('Insulin Injection', 'injection', 'I-3001', 3, 5, '2026-08-20', 2);

-- Sample stock logs for initial data
INSERT INTO stock_logs (medicine_id, change_qty, action, date) VALUES
(1, 100, 'added_new', CURDATE()),
(2, 8, 'added_new', CURDATE()),
(3, 45, 'added_new', CURDATE()),
(4, 3, 'added_new', CURDATE());
