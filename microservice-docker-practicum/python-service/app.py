from flask import Flask, jsonify

app = Flask(__name__)

@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "service": "python-service",
        "language": "Python",
        "framework": "Flask",
        "status": "running"
    })

@app.route("/analytics", methods=["GET"])
def analytics():
    return jsonify({
        "service": "python-service",
        "message": "Python Service digunakan untuk simulasi analisis data sederhana",
        "data": {
            "total_products": 3,
            "total_orders": 2,
            "analysis": "Produk dengan permintaan tinggi perlu diprioritaskan"
        }
    })

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
