import { ApolloServer } from "@apollo/server";
import { expressMiddleware } from "@apollo/server/express4";
import express from "express";
import cors from "cors";

const PORT = Number(process.env.PORT || 4000);
const MAHASISWA_SERVICE_URL = process.env.MAHASISWA_SERVICE_URL || "http://mahasiswa-service:3001";
const JADWAL_SERVICE_URL = process.env.JADWAL_SERVICE_URL || "http://jadwal-service:3002";
const DOSEN_SERVICE_URL = process.env.DOSEN_SERVICE_URL || "http://dosen-service:5000";
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
    console.error(`fetchJson error [${url}]:`, e.message, e.cause?.code || "");
    throw e;
  }
}

function normalizeJadwal(jadwal) {
  return {
    id: jadwal._id || jadwal.id,
    mata_kuliah: jadwal.mata_kuliah,
    kode_mk: jadwal.kode_mk,
    mahasiswa_id: jadwal.mahasiswa_id,
    dosen_id: jadwal.dosen_id,
    hari: jadwal.hari,
    jam_mulai: jadwal.jam_mulai,
    jam_selesai: jadwal.jam_selesai,
    ruangan: jadwal.ruangan,
    mahasiswa_snapshot: jadwal.mahasiswa_snapshot,
    status: jadwal.status,
    createdAt: jadwal.createdAt,
    updatedAt: jadwal.updatedAt
  };
}

const typeDefs = `#graphql
  type Mahasiswa {
    id: ID!
    nim: String
    nama: String
    jurusan: String
    angkatan: Int
  }

  type Dosen {
    id: ID!
    nip: String
    nama: String
    mata_kuliah: String
    email: String
    status: String
  }

  type MahasiswaSnapshot {
    id: Int
    nim: String
    nama: String
    jurusan: String
  }

  type Jadwal {
    id: ID!
    mata_kuliah: String
    kode_mk: String
    mahasiswa_id: Int
    dosen_id: Int
    hari: String
    jam_mulai: String
    jam_selesai: String
    ruangan: String
    mahasiswa_snapshot: MahasiswaSnapshot
    mahasiswa: Mahasiswa
    dosen: Dosen
    status: String
    createdAt: String
    updatedAt: String
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
    mahasiswa_service: ServiceHealth
    jadwal_service: ServiceHealth
    dosen_service: ServiceHealth
    laravel_service: ServiceHealth
  }

  type Query {
    mahasiswa: [Mahasiswa]
    mahasiswaById(id: ID!): Mahasiswa
    dosen: [Dosen]
    dosenById(id: ID!): Dosen
    jadwal: [Jadwal]
    jadwalById(id: ID!): Jadwal
    report: Report
    systemStatus: SystemStatus
  }

  type Mutation {
    createMahasiswa(nim: String!, nama: String!, jurusan: String!, angkatan: Int!): Mahasiswa
    updateMahasiswa(id: ID!, nim: String!, nama: String!, jurusan: String!, angkatan: Int!): Mahasiswa
    deleteMahasiswa(id: ID!): Boolean
    createDosen(nip: String!, nama: String!, mata_kuliah: String!, email: String): Dosen
    updateDosen(id: ID!, nip: String!, nama: String!, mata_kuliah: String!, email: String, status: String): Dosen
    deleteDosen(id: ID!): Boolean
    createJadwal(
      mata_kuliah: String!
      kode_mk: String!
      mahasiswa_id: Int!
      dosen_id: Int!
      hari: String!
      jam_mulai: String!
      jam_selesai: String!
      ruangan: String!
    ): Jadwal
    updateJadwalStatus(id: ID!, status: String!): Jadwal
    deleteJadwal(id: ID!): Boolean
  }
`;

const resolvers = {
  Query: {
    mahasiswa: async () => (await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa`)).data,
    mahasiswaById: async (_, { id }) => (await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa/${id}`)).data,
    dosen: async () => (await fetchJson(`${DOSEN_SERVICE_URL}/dosen`)).data,
    dosenById: async (_, { id }) => (await fetchJson(`${DOSEN_SERVICE_URL}/dosen/${id}`)).data,
    jadwal: async () => (await fetchJson(`${JADWAL_SERVICE_URL}/jadwal`)).data.map(normalizeJadwal),
    jadwalById: async (_, { id }) => normalizeJadwal((await fetchJson(`${JADWAL_SERVICE_URL}/jadwal/${id}`)).data),
    report: async () => (await fetchJson(`${LARAVEL_SERVICE_URL}/report`)).data,
    systemStatus: async () => {
      const [mahasiswaHealth, jadwalHealth, dosenHealth, laravelHealth] = await Promise.all([
        fetchJson(`${MAHASISWA_SERVICE_URL}/health`),
        fetchJson(`${JADWAL_SERVICE_URL}/health`),
        fetchJson(`${DOSEN_SERVICE_URL}/health`),
        fetchJson(`${LARAVEL_SERVICE_URL}/health`)
      ]);
      return {
        mahasiswa_service: mahasiswaHealth,
        jadwal_service: jadwalHealth,
        dosen_service: dosenHealth,
        laravel_service: laravelHealth
      };
    }
  },
  Jadwal: {
    mahasiswa: async (jadwal) => {
      if (!jadwal.mahasiswa_id) return null;
      return (await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa/${jadwal.mahasiswa_id}`)).data;
    },
    dosen: async (jadwal) => {
      if (!jadwal.dosen_id) return null;
      return (await fetchJson(`${DOSEN_SERVICE_URL}/dosen/${jadwal.dosen_id}`)).data;
    }
  },
  Mutation: {
    createMahasiswa: async (_, { nim, nama, jurusan, angkatan }) =>
      (await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa`, {
        method: "POST",
        body: JSON.stringify({ nim, nama, jurusan, angkatan })
      })).data,
    updateMahasiswa: async (_, { id, nim, nama, jurusan, angkatan }) =>
      (await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa/${id}`, {
        method: "PUT",
        body: JSON.stringify({ nim, nama, jurusan, angkatan })
      })).data,
    deleteMahasiswa: async (_, { id }) => {
      await fetchJson(`${MAHASISWA_SERVICE_URL}/mahasiswa/${id}`, { method: "DELETE" });
      return true;
    },
    createDosen: async (_, { nip, nama, mata_kuliah, email }) =>
      (await fetchJson(`${DOSEN_SERVICE_URL}/dosen`, {
        method: "POST",
        body: JSON.stringify({ nip, nama, mata_kuliah, email })
      })).data,
    updateDosen: async (_, { id, nip, nama, mata_kuliah, email, status }) =>
      (await fetchJson(`${DOSEN_SERVICE_URL}/dosen/${id}`, {
        method: "PUT",
        body: JSON.stringify({ nip, nama, mata_kuliah, email, status })
      })).data,
    deleteDosen: async (_, { id }) => {
      await fetchJson(`${DOSEN_SERVICE_URL}/dosen/${id}`, { method: "DELETE" });
      return true;
    },
    createJadwal: async (_, args) =>
      normalizeJadwal((await fetchJson(`${JADWAL_SERVICE_URL}/jadwal`, {
        method: "POST",
        body: JSON.stringify(args)
      })).data),
    updateJadwalStatus: async (_, { id, status }) =>
      normalizeJadwal((await fetchJson(`${JADWAL_SERVICE_URL}/jadwal/${id}/status`, {
        method: "PUT",
        body: JSON.stringify({ status })
      })).data),
    deleteJadwal: async (_, { id }) => {
      await fetchJson(`${JADWAL_SERVICE_URL}/jadwal/${id}`, { method: "DELETE" });
      return true;
    }
  }
};

const app = express();
app.use(express.json());
app.use(cors({ origin: "*" }));

const server = new ApolloServer({ typeDefs, resolvers });
await server.start();

app.use("/graphql", expressMiddleware(server));

app.listen(PORT, "0.0.0.0", () => {
  console.log(`GraphQL Gateway Akademi berjalan pada http://0.0.0.0:${PORT}/graphql`);
});