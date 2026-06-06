<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkademiMS - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/login.js'])
</head>
<body class="login-page" style="background-image: url('/images/bg-login.jpg')">

    <div class="login-card">

        {{-- LEFT PANEL: Gambar --}}
        <div class="card-image-panel">
            <div class="card-image-overlay"></div>
            <div class="card-image-content">
                <div class="brand">
                    <div class="brand-icon mono">AM</div>
                    <span class="brand-name mono">AkademiMS</span>
                </div>
                <div class="card-image-bottom">
                    <div class="welcome-badge">
                        <span class="badge-dot"></span>
                        <span class="mono">Microservice Architecture</span>
                    </div>
                    <h1 class="card-image-title">
                        Kelola Data<br>
                        <em>Akademi</em><br>
                        dengan Mudah
                    </h1>
                    <div class="db-list">
                        <div class="db-item">
                            <div class="db-icon db-mysql">M</div>
                            <div class="db-info">
                                <p class="db-name">MySQL Database</p>
                                <p class="db-desc mono">Data mahasiswa</p>
                            </div>
                            <span class="db-badge mono"><span class="status-dot"></span>online</span>
                        </div>
                        <div class="db-item">
                            <div class="db-icon db-pg">P</div>
                            <div class="db-info">
                                <p class="db-name">PostgreSQL Database</p>
                                <p class="db-desc mono">Data dosen</p>
                            </div>
                            <span class="db-badge mono"><span class="status-dot"></span>online</span>
                        </div>
                        <div class="db-item">
                            <div class="db-icon db-mongo">M</div>
                            <div class="db-info">
                                <p class="db-name">MongoDB Database</p>
                                <p class="db-desc mono">Data jadwal</p>
                            </div>
                            <span class="db-badge mono"><span class="status-dot"></span>online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: Form --}}
        <div class="card-form-panel">
            <div class="form-wrapper">
                <div class="form-header">
                    <h2 class="form-title">Selamat Datang</h2>
                    <p class="form-sub">Masuk untuk mengelola data akademi</p>
                </div>

                <div id="error-msg" class="error-msg hidden">
                    <span class="error-icon">!</span>
                    <p>Username atau password salah!</p>
                </div>

                <div class="field-wrap">
                    <label class="field-label" for="username">Username</label>
                    <input id="username" type="text" placeholder="Masukkan username" class="field-input" autocomplete="username">
                </div>

                <div class="field-wrap">
                    <label class="field-label" for="password">Password</label>
                    <input id="password" type="password" placeholder="Masukkan password" class="field-input" autocomplete="current-password">
                </div>

                <button id="btn-login" class="btn-submit">
                    Masuk ke Dashboard
                </button>

                <div class="demo-box">
                    <div class="demo-icon mono">i</div>
                    <div>
                        <p class="demo-label">Demo credentials</p>
                        <p class="demo-cred mono">admin / admin123</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>