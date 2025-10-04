<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Tampilkan halaman log absensi
     */
    public function index(Request $request): View
    {
        $base = rtrim(config('backend.base_url'), '/');
        $logApiUrl = $base . '/api/attendances/log';
        $deptApiUrl = $base . '/api/departements';

        $filters = [
            'date' => $request->query('date'),
            'departement_id' => $request->query('departement_id'),
        ];

        $logs = [];
        $departments = [];

        try {
            $logResponse = Http::get($logApiUrl, $filters);
            if ($logResponse->successful()) {
                $logs = $logResponse->json()['data'] ?? [];
            }

            $deptResponse = Http::get($deptApiUrl);
            if ($deptResponse->successful()) {
                $departments = $deptResponse->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Handle API error
        }

        return view('attendances.log', compact('logs', 'departments'));
    }

    /**
     * Tampilkan dashboard absensi
     */
    public function dashboard(): View
    {
        $employeeApiUrl = rtrim(config('backend.base_url'), '/') . '/api/employees';
        $employees = [];
        try {
            $response = Http::get($employeeApiUrl);
            if ($response->successful()) {
                $employees = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Jika API gagal biarkan array employees kosong
        }

        return view('attendances.dashboard', compact('employees'));
    }

    /**
     * Aksi absensi (Clock In / Clock Out).
     */
    public function storeAction(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'action' => 'required|string|in:clock_in,clock_out',
        ]);

        $baseUrl = rtrim(config('backend.base_url'), '/') . '/api/attendance';
        $payload = ['employee_id' => (string) $validated['employee_id']];

        try {
            if ($validated['action'] === 'clock_in') {
                $response = Http::post($baseUrl . '/clock-in', $payload);

                if ($response->failed()) {
                    $status = $response->status();
                    $json = $response->json();
                    $error = is_array($json) ? $json['error'] ?? null : null;

                    if ($status === 409) {
                        if (!$error) {
                            $error = 'Anda sudah melakukan absen hari ini.';
                        }
                        return back()->with('error', $error);
                    }

                    if (!$error) {
                        $error = 'Terjadi kesalahan pada server.';
                    }
                    return back()->with('error', $error);
                }

                return back()->with('success', 'Clock in berhasil!');
            } else {
                $response = Http::put($baseUrl . '/clock-out', $payload);

                if ($response->failed()) {
                    $status = $response->status();
                    $json = $response->json();
                    $error = is_array($json) ? $json['error'] ?? null : null;

                    if ($status === 404) {
                        $error = 'Tidak ditemukan sesi absen aktif untuk clock out.';
                    }
                    if (!$error) {
                        $error = 'Terjadi kesalahan pada server.';
                    }
                    return back()->with('error', $error);
                }

                return back()->with('success', 'Clock out berhasil!');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }
}