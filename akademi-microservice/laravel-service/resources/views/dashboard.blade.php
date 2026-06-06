<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AkademiMS Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .mono { font-family: 'Space Mono', monospace; }
        .sidebar { width: 260px; min-height: 100vh; }
        .main { margin-left: 260px; min-height: 100vh; }
        .modal-bg { backdrop-filter: blur(4px); }
        .pulse { animation: pulse-green 2s infinite; }
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
        }
        .nav-item { transition: all 0.2s; }
        .nav-item:hover { background: rgba(99,102,241,0.15); }
        .nav-item.active { background: rgba(99,102,241,0.25); border-left: 3px solid #6366f1; }
        .table-row:hover { background: rgba(99,102,241,0.05); }
        .btn-primary { background: linear-gradient(135deg, #6366f1, #8b5cf6); transition: all 0.2s; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .card { background: #0f172a; border: 1px solid #1e293b; border-radius: 16px; }
        input, select { background: #1e293b !important; border: 1px solid #334155 !important; color: white !important; }
        input:focus, select:focus { border-color: #6366f1 !important; outline: none !important; }
        .toast { animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .glow-line { background: linear-gradient(90deg, transparent, #6366f1, #8b5cf6, transparent); height: 1px; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex">

    <div class="sidebar bg-slate-900 border-r border-slate-800 fixed top-0 left-0 flex flex-col">
        <div class="p-6 border-b border-slate-800">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                    <span class="text-white text-xs font-black mono">AM</span>
                </div>
                <h1 class="text-lg font-black text-white mono">AkademiMS</h1>
            </div>
            <div class="flex items-center gap-2">
                <span id="nav-status-dot" class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                <span id="nav-status-text" class="text-xs text-slate-400">Menghubungkan...</span>
            </div>
        </div>
        <nav class="flex-1 p-4 space-y-1">
            <button onclick="showPage('dashboard')" class="nav-item active w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-slate-300 flex items-center gap-3" id="nav-dashboard">
                <span>📊</span><span>Dashboard</span>
            </button>
            <button onclick="showPage('mahasiswa')" class="nav-item w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-slate-300 flex items-center gap-3" id="nav-mahasiswa">
                <span>🎓</span><span>Mahasiswa</span>
            </button>
            <button onclick="showPage('dosen')" class="nav-item w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-slate-300 flex items-center gap-3" id="nav-dosen">
                <span>👨‍🏫</span><span>Dosen</span>
            </button>
            <button onclick="showPage('jadwal')" class="nav-item w-full text-left px-4 py-3 rounded-xl text-sm font-medium text-slate-300 flex items-center gap-3" id="nav-jadwal">
                <span>📅</span><span>Jadwal</span>
            </button>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 px-4 py-3 mb-2">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center">
                    <span class="text-white text-xs font-bold">A</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-white">Admin</p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
            </div>
            <button onclick="doLogout()" class="w-full px-4 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-950/30 text-left transition-all flex items-center gap-2">
                <span>→</span><span>Keluar</span>
            </button>
        </div>
    </div>

    <div class="main bg-slate-950 w-full">

        <div id="page-dashboard" class="p-8">
            <div class="mb-8">
                <h2 class="text-3xl font-black text-white mb-1">Dashboard</h2>
                <p class="text-slate-400 text-sm">Overview semua microservice akademi</p>
                <div class="glow-line mt-4 rounded-full"></div>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-8" id="stat-cards">
                <div class="card p-6 animate-pulse"><div class="h-4 bg-slate-800 rounded mb-4"></div><div class="h-10 bg-slate-800 rounded"></div></div>
                <div class="card p-6 animate-pulse"><div class="h-4 bg-slate-800 rounded mb-4"></div><div class="h-10 bg-slate-800 rounded"></div></div>
                <div class="card p-6 animate-pulse"><div class="h-4 bg-slate-800 rounded mb-4"></div><div class="h-10 bg-slate-800 rounded"></div></div>
            </div>
            <div>
                <h3 class="text-xs font-semibold text-indigo-400 uppercase tracking-widest mb-4 mono">System Status</h3>
                <div class="grid grid-cols-3 gap-4" id="system-status-cards"></div>
            </div>
        </div>

        <div id="page-mahasiswa" class="p-8 hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white mb-1">Mahasiswa</h2>
                    <p class="text-slate-400 text-sm">Kelola data mahasiswa dari MySQL</p>
                    <div class="glow-line mt-4 rounded-full"></div>
                </div>
                <button onclick="openModal('mahasiswa')" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold text-white flex items-center gap-2">
                    <span>+</span> Tambah Mahasiswa
                </button>
            </div>
            <div class="card overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">NIM</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Nama</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Jurusan</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Angkatan</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mahasiswa-table"></tbody>
                </table>
            </div>
        </div>

        <div id="page-dosen" class="p-8 hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white mb-1">Dosen</h2>
                    <p class="text-slate-400 text-sm">Kelola data dosen dari PostgreSQL</p>
                    <div class="glow-line mt-4 rounded-full"></div>
                </div>
                <button onclick="openModal('dosen')" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold text-white flex items-center gap-2">
                    <span>+</span> Tambah Dosen
                </button>
            </div>
            <div class="card overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">NIP</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Nama</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Mata Kuliah</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Email</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Status</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dosen-table"></tbody>
                </table>
            </div>
        </div>

        <div id="page-jadwal" class="p-8 hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-white mb-1">Jadwal</h2>
                    <p class="text-slate-400 text-sm">Kelola jadwal kelas dari MongoDB</p>
                    <div class="glow-line mt-4 rounded-full"></div>
                </div>
                <button onclick="openModal('jadwal')" class="btn-primary px-5 py-2.5 rounded-xl text-sm font-semibold text-white flex items-center gap-2">
                    <span>+</span> Tambah Jadwal
                </button>
            </div>
            <div class="card overflow-hidden">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800">
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Mata Kuliah</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Hari</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Jam</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Ruangan</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Status</th>
                            <th class="text-left px-6 py-4 text-xs text-slate-400 uppercase tracking-widest mono">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="jadwal-table"></tbody>
                </table>
            </div>
        </div>

    </div>

    <div id="modal" class="hidden fixed inset-0 bg-black/70 modal-bg z-50 flex items-center justify-center">
        <div class="card w-full max-w-md mx-4 p-6" style="border-color: rgba(99,102,241,0.3);">
            <div class="flex items-center justify-between mb-6">
                <h3 id="modal-title" class="text-lg font-bold text-white"></h3>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-all">x</button>
            </div>
            <div class="glow-line mb-6 rounded-full"></div>
            <div id="modal-body" class="space-y-4"></div>
            <div class="flex gap-3 mt-6">
                <button onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-slate-700 text-slate-300 text-sm hover:bg-slate-800 transition-all">Batal</button>
                <button onclick="submitModal()" class="btn-primary flex-1 py-2.5 rounded-xl text-white text-sm font-semibold">Simpan</button>
            </div>
        </div>
    </div>

    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        const GATEWAY = 'http://localhost:4000/graphql';
        let modalType = '';
        let editId = null;
        let mahasiswaList = [];
        let dosenList = [];
        let jadwalList = [];

        if (!sessionStorage.getItem('loggedIn')) {
            window.location.href = '/login';
        }

        async function gql(query, variables = {}) {
            const res = await fetch(GATEWAY, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'x-apollo-operation-name': 'Op' },
                body: JSON.stringify({ query, variables })
            });
            const json = await res.json();
            if (json.errors) throw new Error(json.errors[0].message);
            return json.data;
        }

        function toast(msg, type = 'success') {
            const el = document.createElement('div');
            el.className = `toast px-4 py-3 rounded-xl text-sm font-medium shadow-lg ${type === 'success' ? 'bg-green-900 text-green-200 border border-green-700' : 'bg-red-900 text-red-200 border border-red-700'}`;
            el.textContent = msg;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 3000);
        }

        function doLogout() {
            sessionStorage.removeItem('loggedIn');
            window.location.href = '/login';
        }

        function showPage(page) {
            ['dashboard','mahasiswa','dosen','jadwal'].forEach(p => {
                document.getElementById('page-' + p).classList.add('hidden');
                document.getElementById('nav-' + p).classList.remove('active');
            });
            document.getElementById('page-' + page).classList.remove('hidden');
            document.getElementById('nav-' + page).classList.add('active');
            if (page === 'mahasiswa') loadMahasiswa();
            if (page === 'dosen') loadDosen();
            if (page === 'jadwal') loadJadwal();
        }

        async function loadDashboard() {
            try {
                const data = await gql(`query Op {
                    systemStatus {
                        mahasiswa_service { service database status }
                        jadwal_service { service database status }
                        dosen_service { service language framework database status }
                    }
                    mahasiswa { id }
                    dosen { id }
                    jadwal { id }
                }`);

                document.getElementById('nav-status-dot').className = 'w-2 h-2 rounded-full bg-green-400 pulse inline-block';
                document.getElementById('nav-status-text').textContent = 'Gateway Connected';

                const ms = data.systemStatus.mahasiswa_service;
                const js = data.systemStatus.jadwal_service;
                const ds = data.systemStatus.dosen_service;

                document.getElementById('stat-cards').innerHTML = `
                    <div class="card p-6" style="border-color: rgba(99,102,241,0.2);">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-3">Total Mahasiswa</p>
                        <p class="text-5xl font-black text-white mb-2">${data.mahasiswa.length}</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                            <p class="text-xs text-blue-400">MySQL Database</p>
                        </div>
                    </div>
                    <div class="card p-6" style="border-color: rgba(139,92,246,0.2);">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-3">Total Dosen</p>
                        <p class="text-5xl font-black text-white mb-2">${data.dosen.length}</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>
                            <p class="text-xs text-purple-400">PostgreSQL Database</p>
                        </div>
                    </div>
                    <div class="card p-6" style="border-color: rgba(234,179,8,0.2);">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-3">Total Jadwal</p>
                        <p class="text-5xl font-black text-white mb-2">${data.jadwal.length}</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                            <p class="text-xs text-yellow-400">MongoDB Database</p>
                        </div>
                    </div>
                `;

                document.getElementById('system-status-cards').innerHTML = `
                    <div class="card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full ${ms.status === 'running' ? 'bg-green-400 pulse' : 'bg-red-400'} inline-block"></span>
                            <span class="text-xs mono text-slate-400">${ms.status}</span>
                        </div>
                        <p class="font-bold text-white mb-1">${ms.service}</p>
                        <p class="text-xs text-blue-400">${ms.database}</p>
                    </div>
                    <div class="card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full ${js.status === 'running' ? 'bg-green-400 pulse' : 'bg-red-400'} inline-block"></span>
                            <span class="text-xs mono text-slate-400">${js.status}</span>
                        </div>
                        <p class="font-bold text-white mb-1">${js.service}</p>
                        <p class="text-xs text-yellow-400">${js.database}</p>
                    </div>
                    <div class="card p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2 h-2 rounded-full ${ds.status === 'running' ? 'bg-green-400 pulse' : 'bg-red-400'} inline-block"></span>
                            <span class="text-xs mono text-slate-400">${ds.status}</span>
                        </div>
                        <p class="font-bold text-white mb-1">${ds.service}</p>
                        <p class="text-xs text-slate-500">${ds.framework} - ${ds.language}</p>
                        <p class="text-xs text-purple-400">${ds.database}</p>
                    </div>
                `;
            } catch (err) {
                document.getElementById('nav-status-dot').className = 'w-2 h-2 rounded-full bg-red-400 inline-block';
                document.getElementById('nav-status-text').textContent = 'Disconnected';
                toast('Gagal terhubung ke gateway: ' + err.message, 'error');
            }
        }

        async function loadMahasiswa() {
            const data = await gql(`query Op { mahasiswa { id nim nama jurusan angkatan } }`);
            mahasiswaList = data.mahasiswa;
            document.getElementById('mahasiswa-table').innerHTML = mahasiswaList.map(m => `
                <tr class="table-row border-b border-slate-800/50">
                    <td class="px-6 py-4 text-sm mono text-slate-300">${m.nim}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-white">${m.nama}</td>
                    <td class="px-6 py-4"><span class="bg-indigo-950 text-indigo-300 px-3 py-1 rounded-full text-xs">${m.jurusan}</span></td>
                    <td class="px-6 py-4 text-sm text-slate-300 mono">${m.angkatan}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button onclick="editMahasiswa(${m.id})" class="px-3 py-1.5 rounded-lg bg-indigo-950 text-indigo-300 text-xs hover:bg-indigo-900 transition-all">Edit</button>
                            <button onclick="deleteMahasiswa(${m.id})" class="px-3 py-1.5 rounded-lg bg-red-950 text-red-300 text-xs hover:bg-red-900 transition-all">Hapus</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function loadDosen() {
            const data = await gql(`query Op { dosen { id nip nama mata_kuliah email status } }`);
            dosenList = data.dosen;
            document.getElementById('dosen-table').innerHTML = dosenList.map(d => `
                <tr class="table-row border-b border-slate-800/50">
                    <td class="px-6 py-4 text-sm mono text-slate-300">${d.nip}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-white">${d.nama}</td>
                    <td class="px-6 py-4"><span class="bg-purple-950 text-purple-300 px-3 py-1 rounded-full text-xs">${d.mata_kuliah}</span></td>
                    <td class="px-6 py-4 text-sm text-slate-400">${d.email ?? '-'}</td>
                    <td class="px-6 py-4"><span class="bg-green-950 text-green-300 px-3 py-1 rounded-full text-xs">${d.status}</span></td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <button onclick="editDosen(${d.id})" class="px-3 py-1.5 rounded-lg bg-indigo-950 text-indigo-300 text-xs hover:bg-indigo-900 transition-all">Edit</button>
                            <button onclick="deleteDosen(${d.id})" class="px-3 py-1.5 rounded-lg bg-red-950 text-red-300 text-xs hover:bg-red-900 transition-all">Hapus</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        async function loadJadwal() {
            const data = await gql(`query Op { jadwal { id mata_kuliah kode_mk hari jam_mulai jam_selesai ruangan status } }`);
            jadwalList = data.jadwal;
            document.getElementById('jadwal-table').innerHTML = jadwalList.length === 0
                ? `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">Belum ada jadwal. Tambahkan jadwal baru.</td></tr>`
                : jadwalList.map(j => `
                <tr class="table-row border-b border-slate-800/50">
                    <td class="px-6 py-4 text-sm font-semibold text-white">${j.mata_kuliah}</td>
                    <td class="px-6 py-4 text-sm text-slate-300">${j.hari}</td>
                    <td class="px-6 py-4 text-sm mono text-slate-300">${j.jam_mulai} - ${j.jam_selesai}</td>
                    <td class="px-6 py-4"><span class="bg-yellow-950 text-yellow-300 px-3 py-1 rounded-full text-xs">${j.ruangan}</span></td>
                    <td class="px-6 py-4"><span class="bg-green-950 text-green-300 px-3 py-1 rounded-full text-xs">${j.status ?? 'aktif'}</span></td>
                    <td class="px-6 py-4">
                        <button onclick="deleteJadwal('${j.id}')" class="px-3 py-1.5 rounded-lg bg-red-950 text-red-300 text-xs hover:bg-red-900 transition-all">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        function openModal(type, data = null) {
            modalType = type;
            editId = data ? data.id : null;
            const title = document.getElementById('modal-title');
            const body = document.getElementById('modal-body');

            if (type === 'mahasiswa') {
                title.textContent = editId ? 'Edit Mahasiswa' : 'Tambah Mahasiswa';
                body.innerHTML = `
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">NIM</label><input id="f-nim" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.nim ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Nama</label><input id="f-nama" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.nama ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Jurusan</label><input id="f-jurusan" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.jurusan ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Angkatan</label><input id="f-angkatan" type="number" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.angkatan ?? ''}"></div>
                `;
            } else if (type === 'dosen') {
                title.textContent = editId ? 'Edit Dosen' : 'Tambah Dosen';
                body.innerHTML = `
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">NIP</label><input id="f-nip" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.nip ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Nama</label><input id="f-nama" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.nama ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Mata Kuliah</label><input id="f-mk" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.mata_kuliah ?? ''}"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Email</label><input id="f-email" type="email" class="w-full rounded-xl px-4 py-2.5 text-sm" value="${data?.email ?? ''}"></div>
                    ${editId ? `<div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Status</label><select id="f-status" class="w-full rounded-xl px-4 py-2.5 text-sm"><option value="aktif" ${data?.status === 'aktif' ? 'selected' : ''}>Aktif</option><option value="nonaktif" ${data?.status === 'nonaktif' ? 'selected' : ''}>Nonaktif</option></select></div>` : ''}
                `;
            } else if (type === 'jadwal') {
                title.textContent = 'Tambah Jadwal';
                const hariList = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                body.innerHTML = `
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Mata Kuliah</label><input id="f-mk" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Kode MK</label><input id="f-kode" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">ID Mahasiswa</label><input id="f-mhs-id" type="number" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">ID Dosen</label><input id="f-dsn-id" type="number" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Hari</label><select id="f-hari" class="w-full rounded-xl px-4 py-2.5 text-sm">${hariList.map(h => `<option value="${h}">${h}</option>`).join('')}</select></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Jam Mulai</label><input id="f-jam-mulai" type="time" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Jam Selesai</label><input id="f-jam-selesai" type="time" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                    <div><label class="text-xs text-slate-400 block mb-1 uppercase tracking-widest">Ruangan</label><input id="f-ruangan" type="text" class="w-full rounded-xl px-4 py-2.5 text-sm"></div>
                `;
            }

            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
            editId = null;
            modalType = '';
        }

        async function submitModal() {
            try {
                if (modalType === 'mahasiswa') {
                    const nim = document.getElementById('f-nim').value;
                    const nama = document.getElementById('f-nama').value;
                    const jurusan = document.getElementById('f-jurusan').value;
                    const angkatan = parseInt(document.getElementById('f-angkatan').value);
                    if (editId) {
                        await gql(`mutation Op($id:ID!,$nim:String!,$nama:String!,$jurusan:String!,$angkatan:Int!) { updateMahasiswa(id:$id,nim:$nim,nama:$nama,jurusan:$jurusan,angkatan:$angkatan) { id } }`, { id: editId, nim, nama, jurusan, angkatan });
                        toast('Mahasiswa berhasil diperbarui!');
                    } else {
                        await gql(`mutation Op($nim:String!,$nama:String!,$jurusan:String!,$angkatan:Int!) { createMahasiswa(nim:$nim,nama:$nama,jurusan:$jurusan,angkatan:$angkatan) { id } }`, { nim, nama, jurusan, angkatan });
                        toast('Mahasiswa berhasil ditambahkan!');
                    }
                    closeModal();
                    loadMahasiswa();
                } else if (modalType === 'dosen') {
                    const nip = document.getElementById('f-nip').value;
                    const nama = document.getElementById('f-nama').value;
                    const mata_kuliah = document.getElementById('f-mk').value;
                    const email = document.getElementById('f-email').value;
                    if (editId) {
                        const status = document.getElementById('f-status').value;
                        await gql(`mutation Op($id:ID!,$nip:String!,$nama:String!,$mata_kuliah:String!,$email:String,$status:String) { updateDosen(id:$id,nip:$nip,nama:$nama,mata_kuliah:$mata_kuliah,email:$email,status:$status) { id } }`, { id: editId, nip, nama, mata_kuliah, email, status });
                        toast('Dosen berhasil diperbarui!');
                    } else {
                        await gql(`mutation Op($nip:String!,$nama:String!,$mata_kuliah:String!,$email:String) { createDosen(nip:$nip,nama:$nama,mata_kuliah:$mata_kuliah,email:$email) { id } }`, { nip, nama, mata_kuliah, email });
                        toast('Dosen berhasil ditambahkan!');
                    }
                    closeModal();
                    loadDosen();
                } else if (modalType === 'jadwal') {
                    const mata_kuliah = document.getElementById('f-mk').value;
                    const kode_mk = document.getElementById('f-kode').value;
                    const mahasiswa_id = parseInt(document.getElementById('f-mhs-id').value);
                    const dosen_id = parseInt(document.getElementById('f-dsn-id').value);
                    const hari = document.getElementById('f-hari').value;
                    const jam_mulai = document.getElementById('f-jam-mulai').value;
                    const jam_selesai = document.getElementById('f-jam-selesai').value;
                    const ruangan = document.getElementById('f-ruangan').value;
                    await gql(`mutation Op($mata_kuliah:String!,$kode_mk:String!,$mahasiswa_id:Int!,$dosen_id:Int!,$hari:String!,$jam_mulai:String!,$jam_selesai:String!,$ruangan:String!) { createJadwal(mata_kuliah:$mata_kuliah,kode_mk:$kode_mk,mahasiswa_id:$mahasiswa_id,dosen_id:$dosen_id,hari:$hari,jam_mulai:$jam_mulai,jam_selesai:$jam_selesai,ruangan:$ruangan) { id } }`, { mata_kuliah, kode_mk, mahasiswa_id, dosen_id, hari, jam_mulai, jam_selesai, ruangan });
                    toast('Jadwal berhasil ditambahkan!');
                    closeModal();
                    loadJadwal();
                }
            } catch (err) {
                toast('Error: ' + err.message, 'error');
            }
        }

        function editMahasiswa(id) {
            const m = mahasiswaList.find(x => x.id == id);
            openModal('mahasiswa', m);
        }

        function editDosen(id) {
            const d = dosenList.find(x => x.id == id);
            openModal('dosen', d);
        }

        async function deleteMahasiswa(id) {
            if (!confirm('Yakin hapus mahasiswa ini?')) return;
            try {
                await gql(`mutation Op($id:ID!) { deleteMahasiswa(id:$id) }`, { id });
                toast('Mahasiswa berhasil dihapus!');
                loadMahasiswa();
            } catch (err) {
                toast('Gagal menghapus: ' + err.message, 'error');
            }
        }

        async function deleteDosen(id) {
            if (!confirm('Yakin hapus dosen ini?')) return;
            try {
                await gql(`mutation Op($id:ID!) { deleteDosen(id:$id) }`, { id });
                toast('Dosen berhasil dihapus!');
                loadDosen();
            } catch (err) {
                toast('Gagal menghapus: ' + err.message, 'error');
            }
        }

        async function deleteJadwal(id) {
            if (!confirm('Yakin hapus jadwal ini?')) return;
            try {
                await gql(`mutation Op($id:ID!) { deleteJadwal(id:$id) }`, { id });
                toast('Jadwal berhasil dihapus!');
                loadJadwal();
            } catch (err) {
                toast('Gagal menghapus: ' + err.message, 'error');
            }
        }

        loadDashboard();
    </script>

</body>
</html>