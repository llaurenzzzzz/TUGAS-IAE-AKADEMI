<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkademiMS - Timetable</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b1120;
            background-image:
                linear-gradient(rgba(99,102,241,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.05) 1px, transparent 1px);
            background-size: 36px 36px;
            min-height: 100vh;
            padding: 32px 24px;
            color: #e2e8f0;
            margin: 0;
        }
        .container { max-width: 1100px; margin: 0 auto; }

        /* HEADER */
        .page-header {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 32px;
        }
        .brand { display: flex; align-items: center; gap: 10px; }
        .brand-icon {
            width: 34px; height: 34px; border-radius: 9px;
            background: #4f46e5; display: flex; align-items: center;
            justify-content: center; font-size: 11px; font-weight: 700;
            color: white; font-family: 'Space Mono', monospace;
        }
        .brand-name { font-size: 13px; color: #94a3b8; font-family: 'Space Mono', monospace; }
        .back-btn {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc; padding: 8px 16px; border-radius: 8px;
            font-size: 13px; font-weight: 600; text-decoration: none;
            transition: background 0.2s;
        }
        .back-btn:hover { background: rgba(99,102,241,0.25); }

        /* TITLE */
        .page-title { margin-bottom: 24px; }
        .page-title h1 { font-size: 24px; font-weight: 800; color: white; margin: 0 0 6px 0; }
        .page-title p { font-size: 13px; color: #64748b; margin: 0; }

        /* DB BADGES */
        .db-badges { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
        .db-badge {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 6px 14px; border-radius: 99px; font-size: 11px;
            font-weight: 600; font-family: 'Space Mono', monospace;
        }
        .db-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .badge-mysql  { background: rgba(12,35,64,0.8); border: 1px solid #1e3a5f; color: #60a5fa; }
        .badge-pg     { background: rgba(26,10,46,0.8); border: 1px solid #3b1f6e; color: #c084fc; }
        .badge-mongo  { background: rgba(10,31,10,0.8); border: 1px solid #1a4a1a; color: #4ade80; }

        /* STATS */
        .stats-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 28px; }
        .stat-card {
            background: #080f1e; border: 1px solid rgba(99,102,241,0.15);
            border-radius: 12px; padding: 18px 20px;
        }
        .stat-label { font-size: 10px; color: #475569; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 6px 0; font-family: 'Space Mono', monospace; }
        .stat-value { font-size: 26px; font-weight: 800; margin: 0; }
        .stat-source { font-size: 10px; font-family: 'Space Mono', monospace; margin: 4px 0 0 0; }

        /* TIMETABLE */
        .timetable { display: flex; flex-direction: column; gap: 20px; }

        .day-block {
            background: #080f1e;
            border: 1px solid rgba(99,102,241,0.15);
            border-radius: 16px;
            overflow: hidden;
        }
        .day-header {
            padding: 14px 20px;
            background: rgba(99,102,241,0.08);
            border-bottom: 1px solid rgba(99,102,241,0.1);
            display: flex; align-items: center; gap: 12px;
        }
        .day-name { font-size: 15px; font-weight: 800; color: white; }
        .day-count {
            font-size: 11px; color: #6366f1;
            background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);
            padding: 2px 10px; border-radius: 99px;
            font-family: 'Space Mono', monospace;
        }

        .jadwal-grid { display: flex; flex-direction: column; gap: 0; }

        .jadwal-row {
            display: grid;
            grid-template-columns: 120px 1fr 1fr 1fr 110px;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.15s;
        }
        .jadwal-row:last-child { border-bottom: none; }
        .jadwal-row:hover { background: rgba(99,102,241,0.04); }

        /* Waktu */
        .waktu-col { text-align: center; }
        .waktu-box {
            background: rgba(99,102,241,0.12);
            border: 1px solid rgba(99,102,241,0.25);
            border-radius: 10px; padding: 8px 10px;
            display: inline-block; min-width: 90px;
        }
        .waktu-mulai { font-size: 16px; font-weight: 800; color: #a5b4fc; font-family: 'Space Mono', monospace; }
        .waktu-sep { font-size: 10px; color: #475569; margin: 2px 0; }
        .waktu-selesai { font-size: 13px; color: #6366f1; font-family: 'Space Mono', monospace; }

        /* Mata kuliah */
        .mk-col {}
        .mk-kode {
            display: inline-block; padding: 3px 9px;
            background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);
            border-radius: 6px; color: #a5b4fc; font-size: 10px;
            font-weight: 700; font-family: 'Space Mono', monospace;
            margin-bottom: 5px;
        }
        .mk-nama { font-size: 13px; font-weight: 700; color: white; }
        .mk-ruangan { font-size: 11px; color: #475569; font-family: 'Space Mono', monospace; margin-top: 3px; }

        /* Mahasiswa */
        .person-col { display: flex; align-items: center; gap: 10px; }
        .avatar {
            width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .avatar-blue   { background: #0c2340; border: 1px solid #1e3a5f; color: #60a5fa; }
        .avatar-purple { background: #1a0a2e; border: 1px solid #3b1f6e; color: #c084fc; }
        .person-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .person-sub  { font-size: 11px; color: #475569; font-family: 'Space Mono', monospace; margin-top: 2px; }

        /* Status */
        .status-col { text-align: right; }
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 99px;
            font-size: 11px; font-weight: 600;
            font-family: 'Space Mono', monospace;
            background: #052e16; color: #4ade80;
        }
        .status-dot { width: 5px; height: 5px; border-radius: 50%; background: #4ade80; }

        /* Column headers */
        .jadwal-header {
            display: grid;
            grid-template-columns: 120px 1fr 1fr 1fr 110px;
            gap: 16px;
            padding: 10px 20px;
            border-bottom: 1px solid rgba(99,102,241,0.08);
        }
        .col-label {
            font-size: 10px; font-weight: 600; color: #334155;
            text-transform: uppercase; letter-spacing: 0.08em;
            font-family: 'Space Mono', monospace;
        }
        .col-label.right { text-align: right; }

        .empty-state { padding: 40px; text-align: center; color: #475569; font-size: 13px; }
        .error-box {
            background: rgba(127,29,29,0.15); border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px; padding: 14px 18px; color: #ef4444;
            font-size: 13px; margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="page-header">
        <div class="brand">
            <div class="brand-icon">AM</div>
            <span class="brand-name">AkademiMS</span>
        </div>
        <a href="/dashboard" class="back-btn">← Dashboard</a>
    </div>

    <div class="page-title">
        <h1>🗓️ Timetable Perkuliahan</h1>
        <p>Jadwal mingguan terintegrasi dari 3 database berbeda</p>
    </div>

    <div class="db-badges">
        <span class="db-badge badge-mysql"><span class="dot"></span>MySQL — Data Mahasiswa</span>
        <span class="db-badge badge-pg"><span class="dot"></span>PostgreSQL — Data Dosen</span>
        <span class="db-badge badge-mongo"><span class="dot"></span>MongoDB — Data Jadwal</span>
    </div>

    @if(isset($error))
    <div class="error-box">⚠ {{ $error }}</div>
    @endif

    <div class="stats-row">
        <div class="stat-card">
            <p class="stat-label">Total Jadwal</p>
            <p class="stat-value" style="color:#4ade80">{{ $stats['total_jadwal'] }}</p>
            <p class="stat-source" style="color:#4ade80">● MongoDB</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Mahasiswa</p>
            <p class="stat-value" style="color:#60a5fa">{{ $stats['total_mahasiswa'] }}</p>
            <p class="stat-source" style="color:#60a5fa">● MySQL</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Dosen</p>
            <p class="stat-value" style="color:#c084fc">{{ $stats['total_dosen'] }}</p>
            <p class="stat-source" style="color:#c084fc">● PostgreSQL</p>
        </div>
    </div>

    <div class="timetable">
        @forelse($timetable as $hari => $jadwals)
        <div class="day-block">
            <div class="day-header">
                <span class="day-name">{{ $hari }}</span>
                <span class="day-count">{{ $jadwals->count() }} kelas</span>
            </div>
            <div class="jadwal-header">
                <span class="col-label">Waktu</span>
                <span class="col-label">Mata Kuliah</span>
                <span class="col-label">Mahasiswa <span style="color:#60a5fa">(MySQL)</span></span>
                <span class="col-label">Dosen <span style="color:#c084fc">(PostgreSQL)</span></span>
                <span class="col-label right">Status</span>
            </div>
            <div class="jadwal-grid">
                @foreach($jadwals->sortBy('jam_mulai') as $jadwal)
                <div class="jadwal-row">
                    {{-- Waktu --}}
                    <div class="waktu-col">
                        <div class="waktu-box">
                            <div class="waktu-mulai">{{ $jadwal['jam_mulai'] ?? '-' }}</div>
                            <div class="waktu-sep">──</div>
                            <div class="waktu-selesai">{{ $jadwal['jam_selesai'] ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Mata Kuliah --}}
                    <div class="mk-col">
                        <div class="mk-kode">{{ $jadwal['kode_mk'] ?? 'MK' }}</div>
                        <div class="mk-nama">{{ $jadwal['mata_kuliah'] ?? '-' }}</div>
                        <div class="mk-ruangan">📍 {{ $jadwal['ruangan'] ?? '-' }}</div>
                    </div>

                    {{-- Mahasiswa --}}
                    <div class="person-col">
                        <div class="avatar avatar-blue">
                            {{ strtoupper(substr($jadwal['mahasiswa']['nama'] ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <div class="person-name">{{ $jadwal['mahasiswa']['nama'] ?? 'ID: '.$jadwal['mahasiswa_id'] }}</div>
                            <div class="person-sub">{{ $jadwal['mahasiswa']['nim'] ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Dosen --}}
                    <div class="person-col">
                        <div class="avatar avatar-purple">
                            {{ strtoupper(substr($jadwal['dosen']['nama'] ?? 'D', 0, 1)) }}
                        </div>
                        <div>
                            <div class="person-name">{{ $jadwal['dosen']['nama'] ?? 'ID: '.$jadwal['dosen_id'] }}</div>
                            <div class="person-sub">{{ $jadwal['dosen']['mata_kuliah'] ?? '-' }}</div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="status-col">
                        <span class="status-badge">
                            <span class="status-dot"></span>
                            {{ $jadwal['status'] ?? 'aktif' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="empty-state">Belum ada data jadwal</div>
        @endforelse
    </div>

</div>
</body>
</html>