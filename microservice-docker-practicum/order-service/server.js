const express = require("express");
const app = express();
const PORT = 3002;

app.use(express.json());

const PRODUCT_SERVICE_URL =
  process.env.PRODUCT_SERVICE_URL || "http://product-service:3001";

app.get("/health", (req, res) => {
  res.json({
    service: "order-service",
    status: "running"
  });
});

app.get("/orders", async (req, res) => {
  try {
    const productResponse = await fetch(`${PRODUCT_SERVICE_URL}/products`);
    const productData = await productResponse.json();

    const orders = [
      {
        order_id: 101,
        customer: "Andi",
        product: productData.data[0],
        quantity: 1
      },
      {
        order_id: 102,
        customer: "Budi",
        product: productData.data[1],
        quantity: 2
      }
    ];

    res.json({
      service: "order-service",
      message: "Order Service mengambil data dari Product Service",
      data: orders
    });
  } catch (error) {
    res.status(500).json({
      service: "order-service",
      message: "Gagal mengambil data produk",
      error: error.message
    });
  }
});

app.listen(PORT, () => {
  console.log(`Order Service berjalan pada port ${PORT}`);
});