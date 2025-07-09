<?php
// app/Services/UpnvjApiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpnvjApiService
{
    protected $apiUrl;
    protected $apiUsername;
    protected $apiPassword;
    protected $apiKeySecret;

    public function __construct()
    {
        $this->apiUrl = env('API_URL', 'https://api.upnvj.ac.id/data/auth_mahasiswa');
        $this->apiUsername = env('API_USERNAME', 'uakademik');
        $this->apiPassword = env('API_PASSWORD', 'VTUzcjRrNGRlbTFrMjAyNCYh');
        $this->apiKeySecret = env('API_KEY_SECRET', 'Cspwwxq5SyTOMkq8XYcwZ1PMpYrYCwrv');
    }

    public function authenticate($nim, $password)
    {
        try {

            $response = Http::asForm()
                ->withBasicAuth($this->apiUsername, $this->apiPassword)
                ->withHeaders([
                    'Accept' => '*/*',
                    'User-Agent' => 'Thunder Client (https://www.thunderclient.com)',
                    'X-UPNVJ-API-KEY' => $this->apiKeySecret,
                ])
                ->post($this->apiUrl, [
                    'username' => $nim,
                    'password' => $password,
                ]);

            $data = $response->json();

            // PERBAIKAN: Validasi lebih ketat
            if (isset($data['success']) && $data['success'] === true) {
                // PERBAIKAN: Validasi data pengguna yang dikembalikan
                $userData = $data['data'] ?? [];
                $userInfo = $userData['data'] ?? $userData;

                // Periksa apakah data pengguna lengkap
                if (!isset($userInfo['nama']) || empty(trim($userInfo['nama']))) {
                    return [
                        'success' => false,
                        'message' => 'NIM/NIDN/NIK/Email/Username atau password salah'
                    ];
                }

                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Kredensial tidak valid'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Gagal menghubungi API. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Mendapatkan data dosen berdasarkan periode dan program studi
     */
    public function getLecturers($periodId, $programStudiId)
    {
        try {

            $response = Http::asForm()
                ->withBasicAuth($this->apiUsername, $this->apiPassword)
                ->withHeaders([
                    'API_KEY_NAME' => $this->apiKeyName,
                    'API_KEY_SECRET' => $this->apiKeySecret,
                ])
                ->post('https://api.upnvj.ac.id/data/list_dosen_pengajar', [
                    'id_periode' => $periodId,
                    'id_program_studi' => $programStudiId,
                ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Gagal mendapatkan data dosen'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghubungi API. Silakan coba lagi: ' . $e->getMessage()
            ];
        }
    }
    /**
     * Mendapatkan data referensi program studi
     */
    public function getStudyPrograms()
    {
        try {
            $response = Http::asForm()
                ->withBasicAuth($this->apiUsername, $this->apiPassword)
                ->withHeaders([
                    'API_KEY_NAME' => $this->apiKeyName,
                    'API_KEY_SECRET' => $this->apiKeySecret,
                ])
                ->post('https://api.upnvj.ac.id/data/ref_program_studi');

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Gagal mendapatkan data program studi'
            ];
        } catch (\Exception $e) {

            return [
                'success' => false,
                'message' => 'Gagal menghubungi API. Silakan coba lagi: ' . $e->getMessage()
            ];
        }
    }
}
