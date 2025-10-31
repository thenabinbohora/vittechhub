DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  contact_number VARCHAR(30),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE,
  description TEXT
);

CREATE TABLE products (
  product_id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  image VARCHAR(255),
  cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  quantity INT NOT NULL DEFAULT 0,
  special_features VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(category_id)
);

CREATE TABLE orders (
  order_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status ENUM('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'paid',
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE order_items (
  item_id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(product_id)
);

INSERT INTO users (full_name, email, password_hash, role) VALUES
('Admin User', 'admin@vithub.com.au', '$2y$10$E1jrmQeR9rRXV5nT4mE1qOT4oe4lDCt0YF1qV3oF0v9pG3mGJv2N2', 'admin');
-- Password = Admin@123

INSERT INTO categories (category_name, description) VALUES 
('laptops','All laptops'),('phones','Smartphones'),('accessories','Peripherals');

INSERT INTO products (category_id, name, description, image, cost, quantity, special_features) VALUES
(1,'Dell XPS 13','Ultrabook with 4K display','https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=600&h=400&fit=crop&q=80',1299.99,10,'4K, lightweight'),
(1,'MacBook Pro 16"','M2 chip','https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&h=400&fit=crop&q=80',2499.99,5,'M2'),
(2,'iPhone 15 Pro','Advanced camera','https://i.pinimg.com/736x/cb/2a/d0/cb2ad0bbc24149758f88797d22b54ab7.jpg',999.99,20,'Pro camera'),
(2,'Samsung Galaxy S24','Android AI','https://i.pinimg.com/736x/50/a1/af/50a1af3159848f63ccfd1056f0d2616b.jpg',899.99,15,'AI'),
(3,'Wireless Headphones','ANC','https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&h=400&fit=crop&q=80',299.99,30,'Noise cancelling'),
(3,'Apple Watch Series 9','Health','https://i.pinimg.com/originals/23/aa/02/23aa0275ccd57d2db562a0db72a686a4.jpg',399.99,25,'ECG'),
(3,'JBL Party Speaker','High Bass','https://www.jbhifi.com.au/cdn/shop/files/751938-Product-0-I-638526337808117426.jpg?v=1717037051',499.99,1000,'High Bass'),
(3,'Google Pixel Watch 4','Fitness and Well-being','https://www.jbhifi.com.au/cdn/shop/files/829982-Product-0-I-638907292204044611_1be40b9a-aef3-4104-9fab-ff4905d483d1.jpg?v=1755229932',699.99,1000,'Fitness and Well-being'),
(3,'Marshall Speaker','High Volume','https://www.jbhifi.com.au/cdn/shop/files/840247-Product-0-I-638925558008700069.jpg',359.99,1000,'High Volume');

