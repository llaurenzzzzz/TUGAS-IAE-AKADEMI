<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademi Microservice Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .mono { font-family: 'Space Mono', monospace; }
        .card-glow { box-shadow: 0 0 30px rgba(99, 102, 241, 0.08); }
        .status-online { animation: pulse-green 2s infinite; }
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

    <div class="border-b border-slate-800 bg-slate-900/50 backdrop-blur sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-white mono">AkademiMS</h1>
                <p class="text-xs text-slate-400 mt-0.5">Microservice Dashboard</p>
            </div>
            <div class="flex items-center gap-2">
                <span id="status-dot" class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                <span id="status-text" class="text-sm text-slate-400">Menghubungkan...</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-10" id="main-content">
        <div class="text-center py-32">
            <p class="text-slate-500 text-lg">Memuat data...</p>
        </div>
    </div>

    <div class="border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <p class="text-xs text-slate-600 mono">Laravel Service - Akademi Microservice</p>
            <p class="text-xs text-slate-600">Powered by GraphQL Gateway</p>
        </div>
    </div>

    <script>
        const GATEWAY_URL = 'http://localhost:4000/graphql';

        const query = `
            query DashboardQuery {
                systemStatus {
                    mahasiswa_service { service database status }
                    jadwal_service { service database status }
                    dosen_service { service language framework database status }
                }
                mahasiswa { id nama nim jurusan }
                dosen { id nama nip mata_kuliah }
                jadwal { id mata_kuliah hari jam_mulai jam_selesai ruangan }
                report { total_report report_type generated_by }
            }
        `;

        async function fetchData() {
            try {
                const res = await fetch(GATEWAY_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'x-apollo-operation-name': 'DashboardQuery'
                    },
                    body: JSON.stringify({ query })
                });

                const json = await res.json();

                if (json.errors) {
                    throw new Error(json.errors[0].message);
                }

                const data = json.data;
                if (!data) throw new Error('Data kosong dari gateway');

                document.getElementById('status-dot').className = 'w-2 h-2 rounded-full bg-green-400 status-online inline-block';
                document.getElementById('status-text').textContent = 'Gateway Connected';

                renderDashboard(data);

            } catch (err) {
                document.getElementById('status-dot').className = 'w-2 h-2 rounded-full bg-red-400 inline-block';
                document.getElementById('status-text').textContent = 'Gateway Disconnected';
                document.getElementById('main-content').innerHTML = `
                    <div class="bg-red-950/50 border border-red-800 rounded-xl p-5 mb-8 flex items-start gap-4">
                        <div class="text-red-400 text-xl">!</div>
                        <div>
                            <p class="text-red-300 font-semibold">Gagal terhubung ke GraphQL Gateway</p>
                            <p class="text-red-400/70 text-sm mt-1 mono">${err.message}</p>
                        </div>
                    </div>
                `;
            }
        }

        function statusBadge(status) {
            const isOnline = status === 'online' || status === 'running';
            return `<span class="w-2 h-2 rounded-full ${isOnline ? 'bg-green-400 status-online' : 'bg-red-400'} inline-block"></span>`;
        }

        function renderDashboard(data) {
            const ms = data.systemStatus?.mahasiswa_service ?? {};
            const js = data.systemStatus?.jadwal_service ?? {};
            const ds = data.systemStatus?.dosen_service ?? {};
            const mahasiswa = data.mahasiswa ?? [];
            const dosen = data.dosen ?? [];
            const jadwal = data.jadwal ?? [];
            const report = data.report ?? null;

            document.getElementById('main-content').innerHTML = `
                <div class="mb-10">
                    <h2 class="text-xs font-semibold text-indigo-400 uppercase tracking-widest mb-4 mono">System Status</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 card-glow">
                            <div class="flex items-center gap-2 mb-3">
                                ${statusBadge(ms.status)}
                                <span class="text-xs mono text-slate-400">${ms.status ?? 'unknown'}</span>
                            </div>
                            <p class="font-semibold text-white">${ms.service ?? 'Mahasiswa Service'}</p>
                            <p class="text-xs text-indigo-400 mt-1">${ms.database ?? ''}</p>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 card-glow">
                            <div class="flex items-center gap-2 mb-3">
                                ${statusBadge(js.status)}
                                <span class="text-xs mono text-slate-400">${js.status ?? 'unknown'}</span>
                            </div>
                            <p class="font-semibold text-white">${js.service ?? 'Jadwal Service'}</p>
                            <p class="text-xs text-indigo-400 mt-1">${js.database ?? ''}</p>
                        </div>
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 card-glow">
                            <div class="flex items-center gap-2 mb-3">
                                ${statusBadge(ds.status)}
                                <span class="text-xs mono text-slate-400">${ds.status ?? 'unknown'}</span>
                            </div>
                            <p class="font-semibold text-white">${ds.service ?? 'Dosen Service'}</p>
                            <p class="text-xs text-slate-500 mt-1">${ds.framework ?? ''} - ${ds.language ?? ''}</p>
                            <p class="text-xs text-indigo-400">${ds.database ?? ''}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-1">Total Mahasiswa</p>
                        <p class="text-4xl font-bold text-white">${mahasiswa.length}</p>
                        <p class="text-xs text-indigo-400 mt-2">dari Mahasiswa Service</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-1">Total Dosen</p>
                        <p class="text-4xl font-bold text-white">${dosen.length}</p>
                        <p class="text-xs text-indigo-400 mt-2">dari Dosen Service</p>
                    </div>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <p class="text-xs text-slate-500 uppercase tracking-widest mono mb-1">Total Jadwal</p>
                        <p class="text-4xl font-bold text-white">${jadwal.length}</p>
                        <p class="text-xs text-indigo-400 mt-2">dari Jadwal Service</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
                            Data Mahasiswa
                        </h2>
                        <div class="space-y-3">
                            ${mahasiswa.map(m => `
                            <div class="flex items-center justify-between bg-slate-800/50 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-white">${m.nama}</p>
                                    <p class="text-xs text-slate-400 mono">${m.nim}</p>
                                </div>
                                <span class="text-xs bg-blue-950 text-blue-300 px-3 py-1 rounded-full">${m.jurusan}</span>
                            </div>`).join('')}
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-400 inline-block"></span>
                            Data Dosen
                        </h2>
                        <div class="space-y-3">
                            ${dosen.map(d => `
                            <div class="flex items-center justify-between bg-slate-800/50 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-white">${d.nama}</p>
                                    <p class="text-xs text-slate-400 mono">${d.nip}</p>
                                </div>
                                <span class="text-xs bg-purple-950 text-purple-300 px-3 py-1 rounded-full">${d.mata_kuliah}</span>
                            </div>`).join('')}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                            Data Jadwal
                        </h2>
                        <div class="space-y-3">
                            ${jadwal.map(j => `
                            <div class="flex items-center justify-between bg-slate-800/50 rounded-xl px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-white">${j.mata_kuliah}</p>
                                    <p class="text-xs text-slate-400">${j.hari} - ${j.jam_mulai} s/d ${j.jam_selesai}</p>
                                </div>
                                <span class="text-xs bg-yellow-950 text-yellow-300 px-3 py-1 rounded-full mono">${j.ruangan}</span>
                            </div>`).join('')}
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
                        <h2 class="text-sm font-semibold text-slate-300 mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                            Report Laravel Service
                        </h2>
                        ${report ? `
                        <div class="space-y-3">
                            <div class="bg-slate-800/50 rounded-xl px-4 py-3 flex justify-between">
                                <span class="text-xs text-slate-400">Total Report</span>
                                <span class="text-xs font-semibold text-white mono">${report.total_report}</span>
                            </div>
                            <div class="bg-slate-800/50 rounded-xl px-4 py-3 flex justify-between">
                                <span class="text-xs text-slate-400">Tipe Report</span>
                                <span class="text-xs font-semibold text-white">${report.report_type}</span>
                            </div>
                            <div class="bg-slate-800/50 rounded-xl px-4 py-3 flex justify-between">
                                <span class="text-xs text-slate-400">Generated By</span>
                                <span class="text-xs font-semibold text-green-400">${report.generated_by}</span>
                            </div>
                        </div>` : ''}
                    </div>
                </div>
            `;
        }

        fetchData();
    </script>

</body>
</html>