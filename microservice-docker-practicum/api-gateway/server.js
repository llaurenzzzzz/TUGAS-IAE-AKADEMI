const express = require("express");
const app = express();
const PORT = 3000;

app.use(express.json());

const PRODUCT_SERVICE_URL =
  process.env.PRODUCT_SERVICE_URL || "http://product-service:3001";
const ORDER_SERVICE_URL =
  process.env.ORDER_SERVICE_URL || "http://order-service:3002";
const PYTHON_SERVICE_URL =
  process.env.PYTHON_SERVICE_URL || "http://python-service:5000";
const LARAVEL_SERVICE_URL =
  process.env.LARAVEL_SERVICE_URL || "http://laravel-service:8000";

app.get("/", (req, res) => {
  res.json({
    service: "api-gateway",
    message: "API Gateway Microservice Multi-Platform berjalan",
    endpoints: [
      "/products",
      "/orders",
      "/analytics",
      "/report",
      "/health"
    ],
    services: [
      "Node.js Product Service",
      "Node.js Order Service",
      "Python Flask Service",
      "Laravel PHP Service"
    ]
  });
});

app.get("/health", (req, res) => {
  res.json({
    service: "api-gateway",
    status: "running"
  });
});

app.get("/products", async (req, res) => {
  try {
    const response = await fetch(`${PRODUCT_SERVICE_URL}/products`);
    const data = await response.json();
    res.json({
      gateway: "api-gateway",
      source: "product-service",
      result: data
    });
  } catch (error) {
    res.status(500).json({
      message: "Gagal menghubungi Product Service",
      error: error.message
    });
  }
});

app.get("/orders", async (req, res) => {
  try {
    const response = await fetch(`${ORDER_SERVICE_URL}/orders`);
    const data = await response.json();
    res.json({
      gateway: "api-gateway",
      source: "order-service",
      result: data
    });
  } catch (error) {
    res.status(500).json({
      message: "Gagal menghubungi Order Service",
      error: error.message
    });
  }
});

app.get("/analytics", async (req, res) => {
  try {
    const response = await fetch(`${PYTHON_SERVICE_URL}/analytics`);
    const data = await response.json();
    res.json({
      gateway: "api-gateway",
      source: "python-service",
      result: data
    });
  } catch (error) {
    res.status(500).json({
      message: "Gagal menghubungi Python Service",
      error: error.message
    });
  }
});

app.get("/report", async (req, res) => {
  try {
    const response = await fetch(`${LARAVEL_SERVICE_URL}/report`);
    const data = await response.json();
    res.json({
      gateway: "api-gateway",
      source: "laravel-service",
      result: data
    });
  } catch (error) {
    res.status(500).json({
      message: "Gagal menghubungi Laravel Service",
      error: error.message
    });
  }
});

app.listen(PORT, () => {
  console.log(`API Gateway berjalan pada port ${PORT}`);
});