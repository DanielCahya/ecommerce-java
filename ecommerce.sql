CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description TEXT,
    price DOUBLE,
    stock INT,
    category VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample users (including admin)
INSERT INTO users (name, email, password, role) VALUES
('John Doe', 'john@example.com', 'password123', 'user'),
('Jane Smith', 'jane@example.com', 'pass456', 'user'),
('admin', 'admin@example.com', 'admin123', 'admin');

-- Insert sample products
INSERT INTO products (name, description, price, stock, category) VALUES
('Flash Sale Laptop Gaming', 'High performance gaming laptop with RGB', 15000000, 3, 'Electronics'),
('Smartphone Android Premium', 'Latest flagship Android smartphone', 12000000, 2, 'Electronics'),
('T-Shirt Premium Cotton', 'Premium cotton t-shirt size M-XL', 150000, 50, 'Fashion'),
('Buku Pemrograman Java Advanced', 'Complete guide to Java programming', 250000, 20, 'Books'),
('Mouse Wireless Gaming', 'Ergonomic wireless gaming mouse', 200000, 30, 'Electronics'),
('Sepatu Sneakers Limited', 'Limited edition comfortable sneakers', 500000, 15, 'Fashion'),
('Sofa Minimalis Modern', 'Modern minimalist sofa 3-seater', 5000000, 8, 'Furniture'),
('Meja Kerja Kayu Jati', 'Premium teak wood office desk', 3500000, 5, 'Furniture'),
('Headphone Bluetooth', 'Noise cancelling bluetooth headphone', 800000, 25, 'Electronics'),
('Jaket Kulit Premium', 'Genuine leather jacket', 1500000, 10, 'Fashion');