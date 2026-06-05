CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price INT NOT NULL
);

INSERT INTO products (name, price) VALUES
  ('Laptop', 7500000),
  ('Mouse', 150000),
  ('Keyboard', 350000);
