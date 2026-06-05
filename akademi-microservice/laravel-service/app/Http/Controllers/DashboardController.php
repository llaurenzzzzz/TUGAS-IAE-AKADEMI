<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private $gatewayUrl = 'http://graphql-gateway:4000/graphql';

    public function index()
    {
        try {
            $query = '
                query {
                    systemStatus {
                        mahasiswa_service {
                            service
                            database
                            status
                        }
                        jadwal_service {
                            service
                            database
                            status
                        }
                        dosen_service {
                            service
                            language
                            framework
                            database
                            status
                        }
                    }
                    mahasiswa {
                        id
                        nama
                        nim
                        prodi
                    }
                    dosen {
                        id
                        nama
                        nip
                        matakuliah
                    }
                    jadwal {
                        id
                        matakuliah
                        hari
                        jam
                        ruangan
                    }
                    report {
                        total_report
                        report_type
                        generated_by
                    }
                }
            ';

            $response = Http::retry(3, 1000)
                ->timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'x-apollo-operation-name' => 'DashboardQuery'
                ])
                ->post($this->gatewayUrl, [
                    'query' => $query
                ]);

            $data = $response->json('data');

            return view('dashboard', compact('data'));

        } catch (\Exception $e) {
            $data = null;
            $error = $e->getMessage();
            return view('dashboard', compact('data', 'error'));
        }
    }
}