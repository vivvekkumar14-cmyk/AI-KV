CREATE DATABASE kv;
USE kv;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) DEFAULT NULL,
    `role` ENUM('founder','vip') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `invite_codes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `used_by` INT DEFAULT NULL,   -- user ID when code is used
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `used_at` TIMESTAMP NULL
);

CREATE TABLE `treasury_assets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `value` DECIMAL(20,2) NOT NULL,
    `scarcity_factor` DECIMAL(5,2) DEFAULT 1.0,
    `performance` DECIMAL(5,2) DEFAULT 1.0,
    `ai_allocation` DECIMAL(5,2) DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE treasury (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_name VARCHAR(100),
    value DECIMAL(15,2)
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    proposal VARCHAR(255),
    vote ENUM('yes','no')
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    gateway VARCHAR(50),
    status ENUM('pending','verified','failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_metrics (
    user_id INT PRIMARY KEY,
    allocation_score DECIMAL(5,2)
);

CREATE TABLE treasury_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_name VARCHAR(100),
    value DECIMAL(15,2),
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `founder_keys` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `founder_id` INT NOT NULL,
  `credential_id` VARBINARY(255) NOT NULL,
  `public_key` VARBINARY(255) NOT NULL,
  `sign_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE governance_votes(
  id INT AUTO_INCREMENT PRIMARY KEY,
  proposal TEXT NOT NULL,
  asset_id INT DEFAULT NULL,      -- optional: vote on specific asset
  votes_for INT DEFAULT 0,
  votes_against INT DEFAULT 0,
  status ENUM('open','closed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE webauthn_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    credential_id TEXT,
    public_key TEXT,
    sign_count INT
);

CREATE TABLE allocation_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(15,2),
    status ENUM('pending','approved','rejected') DEFAULT 'pending'
);

-- E-commerce Tables
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    image_url VARCHAR(500),
    file_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    price DECIMAL(10,2),
    quantity INT DEFAULT 1,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    download_token VARCHAR(64) UNIQUE,
    expiry_date TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE payment_settings (
    id INT PRIMARY KEY DEFAULT 1,
    gateway ENUM('razorpay','stripe','paypal') DEFAULT 'razorpay',
    razorpay_key VARCHAR(255),
    razorpay_secret VARCHAR(255),
    stripe_key VARCHAR(255),
    stripe_secret VARCHAR(255),
    paypal_email VARCHAR(255)
);

INSERT INTO invite_codes (code) VALUES ('KVVIP2026'), ('KVMEMBER001'), ('KVEXCLUSIVE2026');
INSERT INTO users (username, role) VALUES ('founder', 'founder');

-- Treasury Assets with AI allocation data
INSERT INTO treasury_assets (name, value, scarcity_factor, performance, ai_allocation) VALUES 
('Gold Reserve', 5000000.00, 0.95, 0.88, 35.5),
('Digital Allocation Pool', 3200000.00, 0.75, 0.92, 28.2),
('Real Estate Holdings', 2800000.00, 0.85, 0.79, 22.8),
('Precious Metals Portfolio', 1500000.00, 0.90, 0.85, 13.5);

-- Sample Governance Proposals
INSERT INTO governance_votes (proposal, asset_id) VALUES 
('Increase Gold Reserve allocation by 5%', 1),
('Rebalance Digital Allocation Pool based on market performance', 2),
('Approve quarterly treasury rebalancing strategy', NULL);

INSERT INTO treasury (asset_name,value) VALUES ('Gold Reserve', 5000000);
INSERT INTO treasury (asset_name,value) VALUES ('Digital Allocation Pool', 3200000);

-- Sample Products
INSERT INTO products (title,description,price,image_url,file_path) VALUES 
('Imperial Jacket','Crafted with premium Italian leather',1200.00,'https://via.placeholder.com/300x400','imperial_jacket.pdf'),
('Sovereign Watch','Precision timepiece with Swiss movement',8500.00,'https://via.placeholder.com/300x400','sovereign_watch.pdf'),
('Sovereign Boots','Handcrafted leather boots',950.00,'https://via.placeholder.com/300x400','sovereign_boots.pdf');

INSERT INTO payment_settings (gateway) VALUES ('razorpay');
