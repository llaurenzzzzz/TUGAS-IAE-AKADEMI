import os, time, psycopg2
from flask import Flask, jsonify, request

app = Flask(__name__)

DB_HOST = os.getenv("DB_HOST", "analytics-db")
DB_NAME = os.getenv("DB_NAME", "analytics_db")
DB_USER = os.getenv("DB_USER", "analytics_user")
DB_PASSWORD = os.getenv("DB_PASSWORD", "analytics_password")
DB_PORT = os.getenv("DB_PORT", "5432")

conn = None

def connect_with_retry(retries=20, delay=3):
    global conn
    for attempt in range(1, retries + 1):
        try:
            conn = psycopg2.connect(host=DB_HOST, database=DB_NAME, user=DB_USER, password=DB_PASSWORD, port=DB_PORT)
            print("Python Service berhasil terhubung ke PostgreSQL")
            return
        except Exception as error:
            print(f"Menunggu PostgreSQL siap... percobaan {attempt}")
            time.sleep(delay)
    raise Exception("Python Service gagal terhubung ke PostgreSQL")

def init_database():
    cursor = conn.cursor()
    cursor.execute("""
        CREATE TABLE IF NOT EXISTS analytics (
            id SERIAL PRIMARY KEY,
            metric_name VARCHAR(100) NOT NULL,
            metric_value INT NOT NULL,
            description TEXT
        )
    """)
    cursor.execute("SELECT COUNT(*) FROM analytics")
    if cursor.fetchone()[0] == 0:
        cursor.execute("""
            INSERT INTO analytics (metric_name, metric_value, description) VALUES
            ('total_products', 3, 'Jumlah produk awal dari Product Service'),
            ('total_orders', 2, 'Jumlah pesanan awal dari Order Service'),
            ('popular_product_score', 85, 'Skor produk paling diminati')
        """)
    conn.commit()
    cursor.close()

@app.route("/health", methods=["GET"])
def health():
    return jsonify({"service": "python-service", "language": "Python", "framework": "Flask", "database": "postgresql", "status": "running"})

@app.route("/analytics", methods=["GET"])
def get_analytics():
    cursor = conn.cursor()
    cursor.execute("SELECT id, metric_name, metric_value, description FROM analytics ORDER BY id ASC")
    rows = cursor.fetchall()
    cursor.close()
    data = [{"id": r[0], "metric_name": r[1], "metric_value": r[2], "description": r[3]} for r in rows]
    return jsonify({"service": "python-service", "database": "postgresql", "data": data})

@app.route("/analytics", methods=["POST"])
def create_analytics():
    body = request.get_json()
    metric_name = body.get("metric_name")
    metric_value = body.get("metric_value")
    description = body.get("description")
    if not metric_name or metric_value is None:
        return jsonify({"message": "metric_name dan metric_value wajib diisi"}), 400
    cursor = conn.cursor()
    cursor.execute("INSERT INTO analytics (metric_name, metric_value, description) VALUES (%s, %s, %s) RETURNING id", (metric_name, metric_value, description))
    new_id = cursor.fetchone()[0]
    conn.commit()
    cursor.close()
    return jsonify({"service": "python-service", "message": "Data analitik berhasil ditambahkan", "data": {"id": new_id, "metric_name": metric_name, "metric_value": metric_value, "description": description}}), 201

if __name__ == "__main__":
    connect_with_retry()
    init_database()
    app.run(host="0.0.0.0", port=5000)