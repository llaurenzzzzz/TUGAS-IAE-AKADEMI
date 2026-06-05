const express = require("express");
const mongoose = require("mongoose");
const app = express();
const PORT = 3002;

app.use(express.json());

const PRODUCT_SERVICE_URL = process.env.PRODUCT_SERVICE_URL || "http://product-service:3001";
const MONGO_URI = process.env.MONGO_URI || "mongodb://order_user:order_password@order-db:27017/order_db?authSource=admin";

const orderSchema = new mongoose.Schema({
  customer_name: { type: String, required: true },
  product_id: { type: Number, required: true },
  quantity: { type: Number, required: true, min: 1 },
  product_snapshot: { id: Number, name: String, price: Number },
  total_price: { type: Number, required: true },
  status: { type: String, default: "created" }
}, { timestamps: true });

const Order = mongoose.model("Order", orderSchema);

async function connectWithRetry(retries = 20, delay = 3000) {
  for (let attempt = 1; attempt <= retries; attempt++) {
    try {
      await mongoose.connect(MONGO_URI);
      console.log("Order Service berhasil terhubung ke MongoDB");
      return;
    } catch (error) {
      console.log(`Menunggu MongoDB siap... percobaan ${attempt}`);
      await new Promise((resolve) => setTimeout(resolve, delay));
    }
  }
  throw new Error("Order Service gagal terhubung ke MongoDB");
}

async function getProductById(productId) {
  const response = await fetch(`${PRODUCT_SERVICE_URL}/products/${productId}`);
  if (!response.ok) throw new Error("Produk tidak ditemukan di Product Service");
  const productResponse = await response.json();
  return productResponse.data;
}

app.get("/health", (req, res) => {
  res.json({ service: "order-service", database: "mongodb", status: "running" });
});

app.get("/orders", async (req, res) => {
  try {
    const orders = await Order.find().sort({ createdAt: -1 });
    res.json({ service: "order-service", database: "mongodb", data: orders });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil data order", error: error.message });
  }
});

app.get("/orders/:id", async (req, res) => {
  try {
    const order = await Order.findById(req.params.id);
    if (!order) return res.status(404).json({ message: "Order tidak ditemukan" });
    res.json({ service: "order-service", database: "mongodb", data: order });
  } catch (error) {
    res.status(500).json({ message: "Gagal mengambil detail order", error: error.message });
  }
});

app.post("/orders", async (req, res) => {
  try {
    const { customer_name, product_id, quantity } = req.body;
    if (!customer_name || !product_id || !quantity) return res.status(400).json({ message: "customer_name, product_id, dan quantity wajib diisi" });
    const product = await getProductById(product_id);
    const total_price = product.price * quantity;
    const order = await Order.create({ customer_name, product_id, quantity, product_snapshot: { id: product.id, name: product.name, price: product.price }, total_price, status: "created" });
    res.status(201).json({ service: "order-service", message: "Order berhasil dibuat", data: order });
  } catch (error) {
    res.status(500).json({ message: "Gagal membuat order", error: error.message });
  }
});

app.put("/orders/:id/status", async (req, res) => {
  try {
    const { status } = req.body;
    const allowedStatus = ["created", "paid", "processed", "completed", "cancelled"];
    if (!allowedStatus.includes(status)) return res.status(400).json({ message: "Status tidak valid", allowed_status: allowedStatus });
    const order = await Order.findByIdAndUpdate(req.params.id, { status }, { new: true });
    if (!order) return res.status(404).json({ message: "Order tidak ditemukan" });
    res.json({ service: "order-service", message: "Status order berhasil diperbarui", data: order });
  } catch (error) {
    res.status(500).json({ message: "Gagal memperbarui status order", error: error.message });
  }
});

app.delete("/orders/:id", async (req, res) => {
  try {
    const order = await Order.findByIdAndDelete(req.params.id);
    if (!order) return res.status(404).json({ message: "Order tidak ditemukan" });
    res.json({ service: "order-service", message: "Order berhasil dihapus" });
  } catch (error) {
    res.status(500).json({ message: "Gagal menghapus order", error: error.message });
  }
});

async function startServer() {
  await connectWithRetry();
  app.listen(PORT, () => console.log(`Order Service berjalan pada port ${PORT}`));
}

startServer();