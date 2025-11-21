ЗАПУСК ПРОЕКТА ОСУЩЕСТВЛЯЕТСЯ ЧЕРЕЗ
php -S localhost:8000


SQL запрос для создания таблиц (БД Camera-Shop-DB, СУБД PostgreSQL)

-- Таблица пользователей
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Адреса/инфо для заказов
CREATE TABLE user_order_info (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    full_name VARCHAR(200),
    phone VARCHAR(30),
    address TEXT,
    city VARCHAR(100),
    postal_code VARCHAR(50)
);

-- Производители
CREATE TABLE manufacturers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL
);

-- Категории
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT
);

-- Товары
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    description TEXT,
    price NUMERIC(10,2) NOT NULL DEFAULT 0,
    category_id INT REFERENCES categories(id),
    manufacturer_id INT REFERENCES manufacturers(id),
    stock INT DEFAULT 0
);
-- Роли
CREATE TABLE roles (
    id INT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Фотки товаров
CREATE TABLE product_images (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    image_url TEXT NOT NULL,
    is_main BOOLEAN DEFAULT FALSE
);

-- Заказы
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    user_info_id INT REFERENCES user_order_info(id),
    total_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(50) DEFAULT 'new',
    created_at TIMESTAMP DEFAULT NOW()
);

-- Товары в заказе
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    price NUMERIC(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1
);

-- Корзина
CREATE TABLE cart (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Товары в корзине
CREATE TABLE cart_items (
    id SERIAL PRIMARY KEY,
    cart_id INT REFERENCES cart(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    quantity INT NOT NULL DEFAULT 1
);

-- Отзывы
CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    product_id INT REFERENCES products(id),
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Архив заказов
CREATE TABLE order_archive (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    user_info_id INT REFERENCES user_order_info(id),
    total_price NUMERIC(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(50),
    created_at TIMESTAMP,
    archived_at TIMESTAMP DEFAULT NOW()
);

-- Товары в архивном заказе
CREATE TABLE order_archive_items (
    id SERIAL PRIMARY KEY,
    order_archive_id INT REFERENCES order_archive(id) ON DELETE CASCADE,
    product_id INT REFERENCES products(id),
    price NUMERIC(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1
);

-- Оплаты
CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    payment_method VARCHAR(100),
    paid BOOLEAN DEFAULT FALSE,
    paid_at TIMESTAMP
);

-- Методы доставки
CREATE TABLE delivery_methods (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price NUMERIC(10,2) DEFAULT 0
);

Для users
ALTER TABLE users
ADD COLUMN role INT NOT NULL DEFAULT 1,
ADD CONSTRAINT fk_user_role FOREIGN KEY (role) REFERENCES roles(id);


ТЕСТОВЫЕ ДАННЫЕ

-- Пользователи
INSERT INTO users (username, email, password) VALUES
('ivanov', 'ivanov@mail.ru', 'pass123'),
('petrov', 'petrov@mail.ru', 'pass456');

-- Адреса пользователей
INSERT INTO user_order_info (user_id, full_name, phone, address, city, postal_code) VALUES
(1, 'Иван Иванов', '+79161234567', 'ул. Ленина, 10', 'Москва', '101000'),
(2, 'Пётр Петров', '+79161234568', 'ул. Пушкина, 5', 'Санкт-Петербург', '191000');

-- Производители
INSERT INTO manufacturers (name) VALUES
('Canon'),
('Nikon'),
('Sony');

-- Категории
INSERT INTO categories (name, description) VALUES
('Фотоаппараты', 'Цифровые фотоаппараты различных типов'),
('Объективы', 'Объективы для фотоаппаратов'),
('Аксессуары', 'Сумки, штативы, карты памяти');

-- Товары
INSERT INTO products (name, description, price, category_id, manufacturer_id, stock) VALUES
('Canon EOS 5D', 'Полнокадровый зеркальный фотоаппарат', 250000, 1, 1, 10),
('Nikon D750', 'Полнокадровый зеркальный фотоаппарат', 200000, 1, 2, 5),
('Sony Alpha a7', 'Беззеркальный фотоаппарат', 220000, 1, 3, 7),
('Canon EF 50mm f/1.8', 'Объектив для Canon', 10000, 2, 1, 15),
('Nikon AF-S 24-70mm', 'Универсальный объектив Nikon', 50000, 2, 2, 8),
('Сумка для фотоаппарата', 'Защитная сумка', 3000, 3, 1, 20);

-- Фотографии товаров
INSERT INTO product_images (product_id, image_url, is_main) VALUES
(1, 'images/canon_eos_5d.jpg', TRUE),
(2, 'images/nikon_d750.jpg', TRUE),
(3, 'images/sony_a7.jpg', TRUE),
(4, 'images/canon_50mm.jpg', TRUE),
(5, 'images/nikon_24_70.jpg', TRUE),
(6, 'images/camera_bag.jpg', TRUE);

-- Корзины
INSERT INTO cart (user_id) VALUES
(1),
(2);

-- Товары в корзине
INSERT INTO cart_items (cart_id, product_id, quantity) VALUES
(1, 1, 1),
(1, 4, 2),
(2, 3, 1),
(2, 6, 1);

-- Заказы
INSERT INTO orders (user_id, user_info_id, total_price, status) VALUES
(1, 1, 270000, 'new'),
(2, 2, 223000, 'processing');

-- Товары в заказе
INSERT INTO order_items (order_id, product_id, price, quantity) VALUES
(1, 1, 250000, 1),
(1, 4, 10000, 2),
(2, 3, 220000, 1),
(2, 6, 3000, 1);

-- Архив заказов
INSERT INTO order_archive (user_id, user_info_id, total_price, status, created_at, archived_at) VALUES
(1, 1, 15000, 'completed', '2025-01-10 12:00:00', NOW());

-- Товары в архивном заказе
INSERT INTO order_archive_items (order_archive_id, product_id, price, quantity) VALUES
(1, 4, 10000, 1),
(1, 6, 5000, 1);

-- Отзывы
INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(1, 1, 5, 'Отличная камера!'),
(2, 3, 4, 'Хороший аппарат, но дорогой');

-- Оплаты
INSERT INTO payments (order_id, payment_method, paid, paid_at) VALUES
(1, 'Карта', FALSE, NULL),
(2, 'PayPal', TRUE, '2025-11-20 14:00:00');

-- Методы доставки
INSERT INTO delivery_methods (name, description, price) VALUES
('Курьером', 'Доставка до двери', 500),
('Почтой России', 'Стандартная доставка', 300),
('Самовывоз', 'Забрать из магазина', 0);


