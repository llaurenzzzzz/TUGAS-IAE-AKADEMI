const express = require("express");
const mongoose = require("mongoose");
const app = express();
const PORT = 3002;

app.use(express.json());

const MAHASISWA_SERVICE_URL = process.env.MAHASISWA_SERVICE_URL || "http://mahasiswa-service:3001";
const MONGO_URI = process.env.MONGO_URI || "mongodb://jadwal_user:jadwal_password@jadwal-db:27017/jadwal_db?authSource=admin";

const jadwalSchema = new mongoose.Schema({
  mata_kuliah: { type: String, required: true },
  kode_mk: { type: String, required: true },
  mahasiswa_id: { type: Number, required: true },
  dosen_id: { type: Number, required: true },
  hari: { type: String, required: true },
  jam_mulai: { type: String, required: true },
  jam_selesai: { type: String, required: true },
  ruangan: { type: String, required: true },
  mahasiswa_snapshot: { id: Number, nim: String, nama: String, jurusan: String },
  status: { type: String, default: "aktif" }
}, { timestamps: true });

const Jadwal = mongoose.model("Jadwal", jadwalSchema);

async function connectWithRetry(retries = 20, delay = 3000) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      await mongoose.connect(MONGO_URI);
      console.log("Jadwal Service berhasil terhubung ke MongoDB");
      return;
    } catch (error) {
      console.log(`Menunggu MongoDB siap... percobaan ${attempt}`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }
  throw new Error("Jadwal Service gagal terhubung ke MongoDB");
}

async function getMahasiswaById(mahasiswaId) {
  const response = await fetch(`${MAHASISWA_SERVICE_URL}/mahasiswa/${mahasiswaId}`);
  if (!response.ok) throw new Error("Mahasiswa tidak ditemukan di Mahasiswa Service");
  const result = await response.json();
  return result.data;
}

app.get("/health", (req, res) => {
  res.json({ service: "jadwal-service", database: "mongodb", status: "running" });
});

app.get("/jadwal", async (req, res) => {
  try {
    const jadwal = await Jadwal.find().sort({ createdAt: -1 });
    res.json({ service: "jadwal-service", database: "mongodb", data: jadwal });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil data jadwal", error: error.message });
  }
});

app.get("/jadwal/:id", async (req, res) => {
  try {
    const jadwal = await Jadwal.findById(req.params.id);
    if (!jadwal) return res.status(404).json({ message: "Jadwal tidak ditemukan" });
    res.json({ service: "jadwal-service", database: "mongodb", data: jadwal });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil detail jadwal", error: error.message });
  }
});

app.post("/jadwal", async (req, res) => {
  try {
    const { mata_kuliah, kode_mk, mahasiswa_id, dosen_id, hari, jam_mulai, jam_selesai, ruangan } = req.body;
    if (!mata_kuliah || !kode_mk || !mahasiswa_id || !dosen_id || !hari || !jam_mulai || !jam_selesai || !ruangan)
      return res.status(400).json({ message: "Semua field wajib diisi" });
    const mahasiswa = await getMahasiswaById(mahasiswa_id);
    const jadwal = await Jadwal.create({
      mata_kuliah, kode_mk, mahasiswa_id, dosen_id, hari, jam_mulai, jam_selesai, ruangan,
      mahasiswa_snapshot: { id: mahasiswa.id, nim: mahasiswa.nim, nama: mahasiswa.nama, jurusan: mahasiswa.jurusan },
      status: "aktif"
    });
    res.status(201).json({ service: "jadwal-service", message: "Jadwal berhasil dibuat", data: jadwal });
  } catch (error) {
    res.status(500).json({ message: "Gagal membuat jadwal", error: error.message });
  }
});

app.put("/jadwal/:id/status", async (req, res) => {
  try {
    const { status } = req.body;
    const allowedStatus = ["aktif", "selesai", "dibatalkan"];
    if (!allowedStatus.includes(status))
      return res.status(400).json({ message: "Status tidak valid", allowed_status: allowedStatus });
    const jadwal = await Jadwal.findByIdAndUpdate(req.params.id, { status }, { new: true });
    if (!jadwal) return res.status(404).json({ message: "Jadwal tidak ditemukan" });
    res.json({ service: "jadwal-service", message: "Status jadwal berhasil diperbarui", data: jadwal });
  } catch (error) {
    res.status(500).json({ message: "Gagal memperbarui status jadwal", error: error.message });
  }
});

app.delete("/jadwal/:id", async (req, res) => {
  try {
    const jadwal = await Jadwal.findByIdAndDelete(req.params.id);
    if (!jadwal) return res.status(404).json({ message: "Jadwal tidak ditemukan" });
    res.json({ service: "jadwal-service", message: "Jadwal berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghapus jadwal", error: error.message });
  }
});

async function startServer() {
  await connectWithRetry();
  app.listen(PORT, () => console.log(`Jadwal Service berjalan pada port ${PORT}`));
}

startServer();
