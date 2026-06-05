import { ApolloServer } from "@apollo/server";
import { startStandaloneServer } from "@apollo/server/standalone";

const PORT = Number(process.env.PORT || 4000);
const PRODUCT_SERVICE_URL = process.env.PRODUCT_SERVICE_URL || "http://product-service:3001";
const ORDER_SERVICE_URL = process.env.ORDER_SERVICE_URL || "http://order-service:3002";
const PYTHON_SERVICE_URL = process.env.PYTHON_SERVICE_URL || "http://python-service:5000";
const LARAVEL_SERVICE_URL = process.env.LARAVEL_SERVICE_URL || "http://laravel-service:8000";

async function fetchJson(url, options = {}) {
  try {
    const response = await fetch(url, {
      signal: AbortSignal.timeout(10000),
      headers: { "Content-Type": "application/json", ...(options.headers || {}) },
      ...options
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || "Request ke service gagal");
    return data;
  } catch (e) {
    console.error(`fetchJson error [${url}]:`, e.message, e.cause?.code || '');
    throw e;
  }
}

function normalizeOrder(order) {
  return {
    id: order._id || order.id,
    customer_name: order.customer_name,
    product_id: order.product_id,
    quantity: order.quantity,
    product_snapshot: order.product_snapshot,
    total_price: order.total_price,
    status: order.status,
    createdAt: order.createdAt,
    updatedAt: order.updatedAt
  };
}

const typeDefs = `#graphql
  type Product {
    id: ID!
    name: String
    price: Int
  }

  type ProductSnapshot {
    id: Int
    name: String
    price: Int
  }

  type Order {
    id: ID!
    customer_name: String
    product_id: Int
    quantity: Int
    product_snapshot: ProductSnapshot
    product: Product
    total_price: Int
    status: String
    createdAt: String
    updatedAt: String
  }

  type Analytics {
    id: ID!
    metric_name: String
    metric_value: Int
    description: String
  }

  type Report {
    total_report: Int
    report_type: String
    generated_by: String
  }

  type ServiceHealth {
    service: String
    language: String
    framework: String
    database: String
    status: String
  }

  type SystemStatus {
    product_service: ServiceHealth
    order_service: ServiceHealth
    python_service: ServiceHealth
    laravel_service: ServiceHealth
  }

  type Query {
    products: [Product]
    product(id: ID!): Product
    orders: [Order]
    order(id: ID!): Order
    analytics: [Analytics]
    report: Report
    systemStatus: SystemStatus
  }

  type Mutation {
    createProduct(name: String!, price: Int!): Product
    createOrder(customer_name: String!, product_id: Int!, quantity: Int!): Order
    createAnalytics(metric_name: String!, metric_value: Int!, description: String): Analytics
    updateOrderStatus(id: ID!, status: String!): Order
    deleteOrder(id: ID!): Boolean
  }
`;

const resolvers = {
  Query: {
    products: async () => (await fetchJson(`${PRODUCT_SERVICE_URL}/products`)).data,
    product: async (_, { id }) => (await fetchJson(`${PRODUCT_SERVICE_URL}/products/${id}`)).data,
    orders: async () => (await fetchJson(`${ORDER_SERVICE_URL}/orders`)).data.map(normalizeOrder),
    order: async (_, { id }) => normalizeOrder((await fetchJson(`${ORDER_SERVICE_URL}/orders/${id}`)).data),
    analytics: async () => (await fetchJson(`${PYTHON_SERVICE_URL}/analytics`)).data,
    report: async () => (await fetchJson(`${LARAVEL_SERVICE_URL}/report`)).data,
    systemStatus: async () => {
      const [productHealth, orderHealth, pythonHealth, laravelHealth] = await Promise.all([
        fetchJson(`${PRODUCT_SERVICE_URL}/health`),
        fetchJson(`${ORDER_SERVICE_URL}/health`),
        fetchJson(`${PYTHON_SERVICE_URL}/health`),
        fetchJson(`${LARAVEL_SERVICE_URL}/health`)
      ]);
      return {
        product_service: productHealth,
        order_service: orderHealth,
        python_service: pythonHealth,
        laravel_service: laravelHealth
      };
    }
  },
  Order: {
    product: async (order) => {
      if (!order.product_id) return null;
      return (await fetchJson(`${PRODUCT_SERVICE_URL}/products/${order.product_id}`)).data;
    }
  },
  Mutation: {
    createProduct: async (_, { name, price }) =>
      (await fetchJson(`${PRODUCT_SERVICE_URL}/products`, { method: "POST", body: JSON.stringify({ name, price }) })).data,
    createOrder: async (_, { customer_name, product_id, quantity }) =>
      normalizeOrder((await fetchJson(`${ORDER_SERVICE_URL}/orders`, { method: "POST", body: JSON.stringify({ customer_name, product_id, quantity }) })).data),
    createAnalytics: async (_, { metric_name, metric_value, description }) =>
      (await fetchJson(`${PYTHON_SERVICE_URL}/analytics`, { method: "POST", body: JSON.stringify({ metric_name, metric_value, description }) })).data,
    updateOrderStatus: async (_, { id, status }) =>
      normalizeOrder((await fetchJson(`${ORDER_SERVICE_URL}/orders/${id}/status`, { method: "PUT", body: JSON.stringify({ status }) })).data),
    deleteOrder: async (_, { id }) => {
      await fetchJson(`${ORDER_SERVICE_URL}/orders/${id}`, { method: "DELETE" });
      return true;
    }
  }
};

const server = new ApolloServer({ typeDefs, resolvers });

const { url } = await startStandaloneServer(server, {
  listen: { host: "0.0.0.0", port: PORT }
});

console.log(`GraphQL Gateway berjalan pada ${url}`);