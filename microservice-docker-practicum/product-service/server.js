const express = require("express");
const mysql = require("mysql2/promise");
const app = express();
const PORT = 3001;

app.use(express.json());

const dbConfig = {
  host: process.env.DB_HOST || "product-db",
  user: process.env.DB_USER || "product_user",
  password: process.env.DB_PASSWORD || "product_password",
  database: process.env.DB_NAME || "product_db",
  port: 3306
};

let db;

async function connectWithRetry(retries = 20, delay = 3000) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      db = await mysql.createConnection(dbConfig);
      console.log("Product Service berhasil terhubung ke MySQL");
      return;
    } catch (error) {
      console.log(`Menunggu MySQL siap... percobaan ${attempt}`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }
  throw new Error("Product Service gagal terhubung ke MySQL");
}

async function initDatabase() {
  await db.execute(`
    CREATE TABLE IF NOT EXISTS products (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      price INT NOT NULL
    )
  `);
  const [rows] = await db.execute("SELECT COUNT(*) AS total FROM products");
  if (rows[0].total === 0) {
    await db.execute(`
      INSERT INTO products (name, price) VALUES
      ('Laptop', 7500000), ('Mouse', 150000), ('Keyboard', 350000)
    `);
  }
}

app.get("/health", (req, res) => {
  res.json({ service: "product-service", database: "mysql", status: "running" });
});

app.get("/products", async (req, res) => {
  try {
    const [products] = await db.execute("SELECT * FROM products");
    res.json({ service: "product-service", database: "mysql", data: products });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil data produk", error: error.message });
  }
});

app.get("/products/:id", async (req, res) => {
  try {
    const [products] = await db.execute("SELECT * FROM products WHERE id = ?", [req.params.id]);
    if (products.length === 0) return res.status(404).json({ message: "Produk tidak ditemukan" });
    res.json({ service: "product-service", database: "mysql", data: products[0] });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil detail produk", error: error.message });
  }
});

app.post("/products", async (req, res) => {
  try {
    const { name, price } = req.body;
    if (!name || !price) return res.status(400).json({ message: "Nama dan harga produk wajib diisi" });
    const [result] = await db.execute("INSERT INTO products (name, price) VALUES (?, ?)", [name, price]);
    res.status(201).json({ service: "product-service", message: "Produk berhasil ditambahkan", data: { id: result.insertId, name, price } });
  } catch (error) {
    res.status(500).json({ message: "Gagal menambahkan produk", error: error.message });
  }
});

app.put("/products/:id", async (req, res) => {
  try {
    const { name, price } = req.body;
    const [result] = await db.execute("UPDATE products SET name = ?, price = ? WHERE id = ?", [name, price, req.params.id]);
    if (result.affectedRows === 0) return res.status(404).json({ message: "Produk tidak ditemukan" });
    res.json({ service: "product-service", message: "Produk berhasil diperbarui", data: { id: Number(req.params.id), name, price } });
  } catch (error) {
    res.status(500).json({ message: "Gagal memperbarui produk", error: error.message });
  }
});

app.delete("/products/:id", async (req, res) => {
  try {
    const [result] = await db.execute("DELETE FROM products WHERE id = ?", [req.params.id]);
    if (result.affectedRows === 0) return res.status(404).json({ message: "Produk tidak ditemukan" });
    res.json({ service: "product-service", message: "Produk berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghapus produk", error: error.message });
  }
});

async function startServer() {
  await connectWithRetry();
  await initDatabase();
  app.listen(PORT, () => console.log(`Product Service berjalan pada port ${PORT}`));
}

startServer();