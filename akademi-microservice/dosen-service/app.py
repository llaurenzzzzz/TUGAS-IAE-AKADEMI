import os, time, psycopg2
from flask import Flask, jsonify, request

app = Flask(__name__)

DB_HOST = os.getenv("DB_HOST", "dosen-db")
DB_NAME = os.getenv("DB_NAME", "dosen_db")
DB_USER = os.getenv("DB_USER", "dosen_user")
DB_PASSWORD = os.getenv("DB_PASSWORD", "dosen_password")
DB_PORT = os.getenv("DB_PORT", "5432")

conn = None

def connect_with_retry(retries=20, delay=3):
    global conn
    for attempt in range(1, retries + 1):
        try:
            conn = psycopg2.connect(
                host=DB_HOST, database=DB_NAME, user=DB_USER,
                password=DB_PASSWORD, port=DB_PORT
            )
            print("Dosen Service berhasil terhubung ke PostgreSQL")
            return
        except Exception as error:
            print(f"Menunggu PostgreSQL siap... percobaan {attempt}")
            time.sleep(delay)
    raise Exception("Dosen Service gagal terhubung ke PostgreSQL")

def init_database():
    cursor = conn.cursor()
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS dosen (
            id SERIAL PRIMARY KEY,
            nip VARCHAR(20) NOT NULL UNIQUE,
            nama VARCHAR(100) NOT NULL,
            mata_kuliah VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            status VARCHAR(20) DEFAULT 'aktif'
        )
    """)
    cursor.execute("SELECT COUNT(*) FROM dosen")
    if cursor.fetchone()[0] == 0:
        cursor.execute("""
            INSERT INTO dosen (nip, nama, mata_kuliah, email, status) VALUES
            ('198001001', 'Dr. Ahmad Fauzi', 'Pemrograman Web', 'ahmad@akademi.ac.id', 'aktif'),
            ('198501002', 'Prof. Siti Rahayu', 'Basis Data', 'siti@akademi.ac.id', 'aktif'),
            ('199001003', 'Dr. Budi Hartono', 'Jaringan Komputer', 'budi@akademi.ac.id', 'aktif')
        """)
    conn.commit()
    cursor.close()

@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "service": "dosen-service",
        "language": "Python",
        "framework": "Flask",
        "database": "postgresql",
        "status": "running"
    })

@app.route("/dosen", methods=["GET"])
def get_dosen():
    cursor = conn.cursor()
    cursor.execute("SELECT id, nip, nama, mata_kuliah, email, status FROM dosen ORDER BY id ASC")
    rows = cursor.fetchall()
    cursor.close()
    data = [{"id": r[0], "nip": r[1], "nama": r[2], "mata_kuliah": r[3], "email": r[4], "status": r[5]} for r in rows]
    return jsonify({"service": "dosen-service", "database": "postgresql", "data": data})

@app.route("/dosen/<int:dosen_id>", methods=["GET"])
def get_dosen_by_id(dosen_id):
    cursor = conn.cursor()
    cursor.execute("SELECT id, nip, nama, mata_kuliah, email, status FROM dosen WHERE id = %s", (dosen_id,))
    row = cursor.fetchone()
    cursor.close()
    if not row:
        return jsonify({"message": "Dosen tidak ditemukan"}), 404
    data = {"id": row[0], "nip": row[1], "nama": row[2], "mata_kuliah": row[3], "email": row[4], "status": row[5]}
    return jsonify({"service": "dosen-service", "database": "postgresql", "data": data})

@app.route("/dosen", methods=["POST"])
def create_dosen():
    body = request.get_json()
    nip = body.get("nip")
    nama = body.get("nama")
    mata_kuliah = body.get("mata_kuliah")
    email = body.get("email")
    if not nip or not nama or not mata_kuliah:
        return jsonify({"message": "nip, nama, dan mata_kuliah wajib diisi"}), 400
    cursor = conn.cursor()
    cursor.execute(
        "INSERT INTO dosen (nip, nama, mata_kuliah, email) VALUES (%s, %s, %s, %s) RETURNING id",
        (nip, nama, mata_kuliah, email)
    )
    new_id = cursor.fetchone()[0]
    conn.commit()
    cursor.close()
    return jsonify({
        "service": "dosen-service",
        "message": "Dosen berhasil ditambahkan",
        "data": {"id": new_id, "nip": nip, "nama": nama, "mata_kuliah": mata_kuliah, "email": email, "status": "aktif"}
    }), 201

@app.route("/dosen/<int:dosen_id>", methods=["DELETE"])
def delete_dosen(dosen_id):
    cursor = conn.cursor()
    cursor.execute("DELETE FROM dosen WHERE id = %s RETURNING id", (dosen_id,))
    deleted = cursor.fetchone()
    conn.commit()
    cursor.close()
    if not deleted:
        return jsonify({"message": "Dosen tidak ditemukan"}), 404
    return jsonify({"service": "dosen-service", "message": "Dosen berhasil dihapus"})

if __name__ == "__main__":
    connect_with_retry()
    init_database()
    app.run(host="0.0.0.0", port=5000)
