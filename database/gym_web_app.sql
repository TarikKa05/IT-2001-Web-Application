CREATE DATABASE IF NOT EXISTS gym_web_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gym_web_app;

DROP TABLE IF EXISTS order_products;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(50)  NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone_number  VARCHAR(20)  NOT NULL,
    date_of_birth DATE         NOT NULL,
    role          ENUM('admin', 'customer') NOT NULL DEFAULT 'customer'
);

CREATE TABLE categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(50)  NOT NULL UNIQUE,
    description VARCHAR(500) NULL
);

CREATE TABLE products (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(50)   NOT NULL UNIQUE,
    description    VARCHAR(500)  NULL,
    price          DECIMAL(10,2) NOT NULL,
    stock_quantity INT           NOT NULL DEFAULT 0,
    is_available   TINYINT(1)    NOT NULL DEFAULT 1,
    CONSTRAINT chk_products_price CHECK (price >= 0),
    CONSTRAINT chk_products_stock CHECK (stock_quantity >= 0)
);

CREATE TABLE product_categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    product_id  INT NOT NULL,
    category_id INT NOT NULL,
    CONSTRAINT fk_product_categories_product  FOREIGN KEY (product_id)  REFERENCES products(id),
    CONSTRAINT fk_product_categories_category FOREIGN KEY (category_id) REFERENCES categories(id),
    CONSTRAINT uq_product_categories UNIQUE (product_id, category_id)
);

CREATE TABLE orders (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT           NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT chk_orders_total CHECK (total_amount >= 0)
);

CREATE TABLE order_products (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    order_id   INT           NOT NULL,
    product_id INT           NOT NULL,
    quantity   INT           NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_order_products_order   FOREIGN KEY (order_id)   REFERENCES orders(id),
    CONSTRAINT fk_order_products_product FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT uq_order_products UNIQUE (order_id, product_id),
    CONSTRAINT chk_order_products_quantity CHECK (quantity > 0),
    CONSTRAINT chk_order_products_price CHECK (unit_price >= 0)
);

INSERT INTO users (name, username, email, password_hash, phone_number, date_of_birth, role) VALUES
('Gym Club Admin', 'admin', 'admin@gymclub.ba', '$2y$10$S/vJY/AUla4McRWGdBaOQeGkJNN4hokwLN1zm7hWzIkkwKKyv0JlW', '0603210136', '1995-05-15', 'admin'),
('Tarik Korman', 'tarikk', 'tarik@gymclub.ba', '$2y$10$ZTr.00SafIl6nJmecrqB9eaygzVx/Bl9.cU0Q/Xe.9yb7seirWdQa', '060000000', '2000-01-01', 'customer'),
('Amina Hadzic', 'aminah', 'amina@gymclub.ba', '$2y$10$EYK1uqHsFkvjAiAN6M/unOnU5rJJ0xa9JvTOXrfhZyfS2I.2p2/eS', '061111222', '2001-07-23', 'customer');

INSERT INTO categories (name, description) VALUES
('Protein', 'Protein supplements for muscle building.'),
('Performance', 'Supplements that increase strength and performance.'),
('Health', 'Supplements for heart health and overall wellness.'),
('Recovery', 'Supplements that help muscles recover after training.');

INSERT INTO products (name, description, price, stock_quantity, is_available) VALUES
('WHEY PROTEIN', 'High-quality protein for muscle building.', 40.00, 10, 1),
('CREATINE', 'Increase strength and performance.', 30.00, 10, 1),
('OMEGA 3', 'Supports heart health and overall wellness.', 15.00, 10, 1);

INSERT INTO product_categories (product_id, category_id) VALUES
(1, 1),
(1, 4),
(2, 2),
(3, 3);

INSERT INTO orders (user_id, total_amount, order_date) VALUES
(2, 70.00, '2026-10-12 14:30:00'),
(2, 30.00, '2026-10-25 18:05:00'),
(3, 55.00, '2026-10-28 10:15:00');

INSERT INTO order_products (order_id, product_id, quantity, unit_price) VALUES
(1, 1, 1, 40.00),
(1, 2, 1, 30.00),
(2, 3, 2, 15.00),
(3, 1, 1, 40.00),
(3, 3, 1, 15.00);
