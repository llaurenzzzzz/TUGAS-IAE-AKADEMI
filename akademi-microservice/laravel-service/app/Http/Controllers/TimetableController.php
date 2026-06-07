<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class TimetableController extends Controller
{
    private $jadwalUrl;
    private $mahasiswaUrl;
    private $dosenUrl;

    public function __construct()
    {
        $this->jadwalUrl    = env('JADWAL_SERVICE_URL', 'http://jadwal-service:3002');
        $this->mahasiswaUrl = env('MAHASISWA_SERVICE_URL', 'http://mahasiswa-service:3001');
        $this->dosenUrl     = env('DOSEN_SERVICE_URL', 'http://dosen-service:5000');
    }

    public function index()
    {
        try {
            $jadwalRes    = Http::timeout(5)->get("{$this->jadwalUrl}/jadwal");
            $mahasiswaRes = Http::timeout(5)->get("{$this->mahasiswaUrl}/mahasiswa");
            $dosenRes     = Http::timeout(5)->get("{$this->dosenUrl}/dosen");

            $jadwals       = $jadwalRes->successful()    ? ($jadwalRes->json()['data'] ?? [])    : [];
            $mahasiswaList = $mahasiswaRes->successful() ? ($mahasiswaRes->json()['data'] ?? []) : [];
            $dosenList     = $dosenRes->successful()     ? ($dosenRes->json()['data'] ?? [])     : [];

            $mahasiswaMap = collect($mahasiswaList)->keyBy('id');
            $dosenMap     = collect($dosenList)->keyBy('id');

            // Gabungkan & group by hari
            $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Minggu' => 7];

            $timetable = collect($jadwals)
                ->map(function ($jadwal) use ($mahasiswaMap, $dosenMap) {
                    $jadwal['mahasiswa'] = $mahasiswaMap->get($jadwal['mahasiswa_id'] ?? null);
                    $jadwal['dosen']     = $dosenMap->get($jadwal['dosen_id'] ?? null);
                    return $jadwal;
                })
                ->groupBy('hari')
                ->sortBy(fn($_, $hari) => $hariOrder[$hari] ?? 99);

            $stats = [
                'total_jadwal'    => count($jadwals),
                'total_mahasiswa' => $mahasiswaMap->count(),
                'total_dosen'     => $dosenMap->count(),
            ];

            return view('timetable', compact('timetable', 'stats'));

        } catch (\Exception $e) {
            return view('timetable', [
                'timetable' => collect([]),
                'stats'     => ['total_jadwal' => 0, 'total_mahasiswa' => 0, 'total_dosen' => 0],
                'error'     => $e->getMessage()
            ]);
        }
    }
}