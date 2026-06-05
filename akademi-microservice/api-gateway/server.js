const express = require("express");
const app = express();
const PORT = 3000;

app.use(express.json());

const MAHASISWA_SERVICE_URL = process.env.MAHASISWA_SERVICE_URL || "http://mahasiswa-service:3001";
const JADWAL_SERVICE_URL = process.env.JADWAL_SERVICE_URL || "http://jadwal-service:3002";
const DOSEN_SERVICE_URL = process.env.DOSEN_SERVICE_URL || "http://dosen-service:5000";
const LARAVEL_SERVICE_URL = process.env.LARAVEL_SERVICE_URL || "http://laravel-service:8000";

app.get("/", (req, res) => {
  res.json({
    service: "api-gateway",
    message: "API Gateway Akademi Microservice berjalan",
    endpoints: ["/mahasiswa", "/jadwal", "/dosen", "/report", "/health"]
  });
});

app.get("/health", (req, res) => {
  res.json({ service: "api-gateway", status: "running" });
});

app.get("/mahasiswa", async (req, res) => {
  try {
    const response = await fetch(`${MAHASISWA_SERVICE_URL}/mahasiswa`);
    const data = await response.json();
    res.json({ gateway: "api-gateway", source: "mahasiswa-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Mahasiswa Service", error: error.message });
  }
});

app.get("/mahasiswa/:id", async (req, res) => {
  try {
    const response = await fetch(`${MAHASISWA_SERVICE_URL}/mahasiswa/${req.params.id}`);
    const data = await response.json();
    res.json({ gateway: "api-gateway", source: "mahasiswa-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Mahasiswa Service", error: error.message });
  }
});

app.post("/mahasiswa", async (req, res) => {
  try {
    const response = await fetch(`${MAHASISWA_SERVICE_URL}/mahasiswa`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(req.body)
    });
    const data = await response.json();
    res.status(response.status).json({ gateway: "api-gateway", source: "mahasiswa-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Mahasiswa Service", error: error.message });
  }
});

app.get("/jadwal", async (req, res) => {
  try {
    const response = await fetch(`${JADWAL_SERVICE_URL}/jadwal`);
    const data = await response.json();
    res.json({ gateway: "api-gateway", source: "jadwal-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Jadwal Service", error: error.message });
  }
});

app.post("/jadwal", async (req, res) => {
  try {
    const response = await fetch(`${JADWAL_SERVICE_URL}/jadwal`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(req.body)
    });
    const data = await response.json();
    res.status(response.status).json({ gateway: "api-gateway", source: "jadwal-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Jadwal Service", error: error.message });
  }
});

app.get("/dosen", async (req, res) => {
  try {
    const response = await fetch(`${DOSEN_SERVICE_URL}/dosen`);
    const data = await response.json();
    res.json({ gateway: "api-gateway", source: "dosen-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Dosen Service", error: error.message });
  }
});

app.post("/dosen", async (req, res) => {
  try {
    const response = await fetch(`${DOSEN_SERVICE_URL}/dosen`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(req.body)
    });
    const data = await response.json();
    res.status(response.status).json({ gateway: "api-gateway", source: "dosen-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Dosen Service", error: error.message });
  }
});

app.get("/report", async (req, res) => {
  try {
    const response = await fetch(`${LARAVEL_SERVICE_URL}/report`);
    const data = await response.json();
    res.json({ gateway: "api-gateway", source: "laravel-service", result: data });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghubungi Laravel Service", error: error.message });
  }
});

app.listen(PORT, () => console.log(`API Gateway berjalan pada port ${PORT}`));
