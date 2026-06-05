CREATE TABLE IF NOT EXISTS analytics (
  id SERIAL PRIMARY KEY,
  metric_name VARCHAR(100) NOT NULL,
  metric_value INT NOT NULL,
  description TEXT
);

INSERT INTO analytics (metric_name, metric_value, description) VALUES
  ('total_products', 3, 'Jumlah produk tersedia'),
  ('total_orders', 2, 'Jumlah order masuk'),
  ('revenue', 7800000, 'Total pendapatan');
