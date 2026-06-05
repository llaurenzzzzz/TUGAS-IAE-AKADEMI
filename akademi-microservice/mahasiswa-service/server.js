const express = require("express");
const mysql = require("mysql2/promise");
const app = express();
const PORT = 3001;

app.use(express.json());

const dbConfig = {
  host: process.env.DB_HOST || "mahasiswa-db",
  user: process.env.DB_USER || "mahasiswa_user",
  password: process.env.DB_PASSWORD || "mahasiswa_password",
  database: process.env.DB_NAME || "mahasiswa_db",
  port: 3306
};

let db;

async function connectWithRetry(retries = 20, delay = 3000) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      db = await mysql.createConnection(dbConfig);
      console.log("Mahasiswa Service berhasil terhubung ke MySQL");
      return;
    } catch (error) {
      console.log(`Menunggu MySQL siap... percobaan ${attempt}`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }
  throw new Error("Mahasiswa Service gagal terhubung ke MySQL");
}

async function initDatabase() {
  await db.execute(`
    CREATE TABLE IF NOT EXISTS mahasiswa (
      id INT AUTO_INCREMENT PRIMARY KEY,
      nim VARCHAR(20) NOT NULL UNIQUE,
      nama VARCHAR(100) NOT NULL,
      jurusan VARCHAR(100) NOT NULL,
      angkatan INT NOT NULL
    )
  `);
  const [rows] = await db.execute("SELECT COUNT(*) AS total FROM mahasiswa");
  if (rows[0].total === 0) {
    await db.execute(`
      INSERT INTO mahasiswa (nim, nama, jurusan, angkatan) VALUES
      ('2021001', 'Andi Pratama', 'Teknik Informatika', 2021),
      ('2021002', 'Budi Santoso', 'Sistem Informasi', 2021),
      ('2022001', 'Citra Dewi', 'Teknik Informatika', 2022)
    `);
  }
}

app.get("/health", (req, res) => {
  res.json({ service: "mahasiswa-service", database: "mysql", status: "running" });
});

app.get("/mahasiswa", async (req, res) => {
  try {
    const [mahasiswa] = await db.execute("SELECT * FROM mahasiswa");
    res.json({ service: "mahasiswa-service", database: "mysql", data: mahasiswa });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil data mahasiswa", error: error.message });
  }
});

app.get("/mahasiswa/:id", async (req, res) => {
  try {
    const [mahasiswa] = await db.execute("SELECT * FROM mahasiswa WHERE id = ?", [req.params.id]);
    if (mahasiswa.length === 0) return res.status(404).json({ message: "Mahasiswa tidak ditemukan" });
    res.json({ service: "mahasiswa-service", database: "mysql", data: mahasiswa[0] });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil detail mahasiswa", error: error.message });
  }
});

app.post("/mahasiswa", async (req, res) => {
  try {
    const { nim, nama, jurusan, angkatan } = req.body;
    if (!nim || !nama || !jurusan || !angkatan)
      return res.status(400).json({ message: "nim, nama, jurusan, dan angkatan wajib diisi" });
    const [result] = await db.execute(
      "INSERT INTO mahasiswa (nim, nama, jurusan, angkatan) VALUES (?, ?, ?, ?)",
      [nim, nama, jurusan, angkatan]
    );
    res.status(201).json({
      service: "mahasiswa-service",
      message: "Mahasiswa berhasil ditambahkan",
      data: { id: result.insertId, nim, nama, jurusan, angkatan }
    });
  } catch (error) {
    res.status(500).json({ message: "Gagal menambahkan mahasiswa", error: error.message });
  }
});

app.put("/mahasiswa/:id", async (req, res) => {
  try {
    const { nim, nama, jurusan, angkatan } = req.body;
    const [result] = await db.execute(
      "UPDATE mahasiswa SET nim = ?, nama = ?, jurusan = ?, angkatan = ? WHERE id = ?",
      [nim, nama, jurusan, angkatan, req.params.id]
    );
    if (result.affectedRows === 0) return res.status(404).json({ message: "Mahasiswa tidak ditemukan" });
    res.json({
      service: "mahasiswa-service",
      message: "Mahasiswa berhasil diperbarui",
      data: { id: Number(req.params.id), nim, nama, jurusan, angkatan }
    });
  } catch (error) {
    res.status(500).json({ message: "Gagal memperbarui mahasiswa", error: error.message });
  }
});

app.delete("/mahasiswa/:id", async (req, res) => {
  try {
    const [result] = await db.execute("DELETE FROM mahasiswa WHERE id = ?", [req.params.id]);
    if (result.affectedRows === 0) return res.status(404).json({ message: "Mahasiswa tidak ditemukan" });
    res.json({ service: "mahasiswa-service", message: "Mahasiswa berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghapus mahasiswa", error: error.message });
  }
});

async function startServer() {
  await connectWithRetry();
  await initDatabase();
  app.listen(PORT, () => console.log(`Mahasiswa Service berjalan pada port ${PORT}`));
}

startServer();
