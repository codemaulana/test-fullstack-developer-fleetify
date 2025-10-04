<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $api = rtrim(config('backend.base_url'), '/') . '/api/employees';
        $deptApi = rtrim(config('backend.base_url'), '/') . '/api/departements';
        
        $q = (string) $request->query('q', '');
        $departementId = (string) $request->query('departement_id', '');

        $employees = [];
        $departments = [];

        try {
            // Pass search and filter params to backend API
            $res = Http::get($api, [
                'q' => $q !== '' ? $q : null,
                'departement_id' => $departementId !== '' ? $departementId : null,
            ]);
            if ($res->successful()) {
                $employees = $res->json()['data'] ?? [];
            }

            // Load departements for the filter dropdown
            $res2 = Http::get($deptApi);
            if ($res2->successful()) {
                $departments = $res2->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // ignore network/API errors, render with whatever data we have
        }

        return view('employees.index', compact('employees', 'departments', 'q', 'departementId'));
    }

    public function create(): View
    {
        $deptApi = rtrim(config('backend.base_url'), '/') . '/api/departements';
        $departments = [];
        try {
            $res = Http::get($deptApi);
            if ($res->successful()) {
                $departments = $res->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // ignore
        }
        return view('employees.create', compact('departments'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'address' => 'required|string',
            'departement_id' => 'required|integer',
        ]);

        $api = rtrim(config('backend.base_url'), '/') . '/api/employees';
        try {
            $res = Http::post($api, [
                'employee_id' => $validated['employee_id'],
                'name' => $validated['name'],
                'address' => $validated['address'],
                'departement_id' => (int) $validated['departement_id'],
            ]);
            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal membuat karyawan') : 'Gagal membuat karyawan';
                return back()->withInput()->with('error', $error);
            }
            return redirect()->route('employees.index')->with('success', 'Karyawan berhasil dibuat');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }

    public function edit(string $id): View
    {
        $empApi = rtrim(config('backend.base_url'), '/') . '/api/employees/' . urlencode($id);
        $deptApi = rtrim(config('backend.base_url'), '/') . '/api/departements';
        $employee = null;
        $departments = [];
        try {
            $res = Http::get($empApi);
            if ($res->successful()) {
                $employee = $res->json()['data'] ?? null;
            }
            $res2 = Http::get($deptApi);
            if ($res2->successful()) {
                $departments = $res2->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // ignore
        }
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(string $id, Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'name' => 'required|string',
            'address' => 'required|string',
            'departement_id' => 'required|integer',
        ]);

        $api = rtrim(config('backend.base_url'), '/') . '/api/employees/' . urlencode($id);
        try {
            $res = Http::put($api, [
                'employee_id' => $validated['employee_id'],
                'name' => $validated['name'],
                'address' => $validated['address'],
                'departement_id' => (int) $validated['departement_id'],
            ]);
            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal memperbarui karyawan') : 'Gagal memperbarui karyawan';
                return back()->withInput()->with('error', $error);
            }
            return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }

    public function destroy(string $id, Request $request): \Illuminate\Http\RedirectResponse
    {
        $apiBase = rtrim(config('backend.base_url'), '/') . '/api/employees/' . urlencode($id);

        $cascade = $request->input('cascade', $request->query('cascade', null));

        $backendUrl = $apiBase;
        if (!empty($cascade) && (string) $cascade === '1') {
            $backendUrl = $apiBase . '?cascade=1';
        }

        try {
            $client = Http::withHeaders([]);
            if (!empty($cascade) && (string) $cascade === '1') {
                $client = Http::withHeaders(['X-Cascade' => '1']);
            }

            $res = $client->delete($backendUrl);

            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal menghapus karyawan') : 'Gagal menghapus karyawan';
                return back()->with('error', $error);
            }

            $successMsg = (!empty($cascade) && (string) $cascade === '1')
                ? 'Karyawan beserta seluruh log berhasil dihapus'
                : 'Data karyawan berhasil dihapus';

            return redirect()->route('employees.index')->with('success', $successMsg);
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }
}