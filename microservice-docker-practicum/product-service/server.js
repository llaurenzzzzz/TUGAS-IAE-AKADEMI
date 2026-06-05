const express = require("express");
const app = express();
const PORT = 3001;

app.use(express.json());

const products = [
  { id: 1, name: "Laptop", price: 7500000 },
  { id: 2, name: "Mouse", price: 150000 },
  { id: 3, name: "Keyboard", price: 350000 }
];

app.get("/health", (req, res) => {
  res.json({
    service: "product-service",
    status: "running"
  });
});

app.get("/products", (req, res) => {
  res.json({
    service: "product-service",
    data: products
  });
});

app.listen(PORT, () => {
  console.log(`Product Service berjalan pada port ${PORT}`);
});