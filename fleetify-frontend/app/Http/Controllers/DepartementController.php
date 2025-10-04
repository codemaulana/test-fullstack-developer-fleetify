<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DepartementController extends Controller
{
    public function index(): View
    {
        $api = rtrim(config('backend.base_url'), '/') . '/api/departements';
        $departements = [];
        try {
            $res = Http::get($api);
            if ($res->successful()) {
                $departements = $res->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // ignore
        }
        return view('departements.index', compact('departements'));
    }

    public function create(): View
    {
        return view('departements.create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'departement_name' => 'required|string',
            'max_clock_in_time' => 'required|string',
            'max_clock_out_time' => 'required|string',
        ]);

        $api = rtrim(config('backend.base_url'), '/') . '/api/departements';
        try {
            $res = Http::post($api, [
                'departement_name' => $validated['departement_name'],
                'max_clock_in_time' => $validated['max_clock_in_time'],
                'max_clock_out_time' => $validated['max_clock_out_time'],
            ]);
            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal membuat departemen') : 'Gagal membuat departemen';
                return back()->withInput()->with('error', $error);
            }
            return redirect()->route('departements.index')->with('success', 'Departemen berhasil dibuat');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }

    public function edit(string $id): View
    {
        $api = rtrim(config('backend.base_url'), '/') . '/api/departements/' . urlencode($id);
        $departement = null;
        try {
            $res = Http::get($api);
            if ($res->successful()) {
                $departement = $res->json()['data'] ?? null;
            }
        } catch (\Exception $e) {
            // ignore
        }
        return view('departements.edit', compact('departement'));
    }

    public function update(string $id, Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'departement_name' => 'required|string',
            'max_clock_in_time' => 'required|string',
            'max_clock_out_time' => 'required|string',
        ]);

        $api = rtrim(config('backend.base_url'), '/') . '/api/departements/' . urlencode($id);
        try {
            $res = Http::put($api, [
                'departement_name' => $validated['departement_name'],
                'max_clock_in_time' => $validated['max_clock_in_time'],
                'max_clock_out_time' => $validated['max_clock_out_time'],
            ]);
            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal memperbarui departemen') : 'Gagal memperbarui departemen';
                return back()->withInput()->with('error', $error);
            }
            return redirect()->route('departements.index')->with('success', 'Departemen berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }

    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $api = rtrim(config('backend.base_url'), '/') . '/api/departements/' . urlencode($id);
        try {
            $res = Http::delete($api);
            if ($res->failed()) {
                $json = $res->json();
                $error = is_array($json) ? ($json['error'] ?? 'Gagal menghapus departemen') : 'Gagal menghapus departemen';
                return back()->with('error', $error);
            }
            return redirect()->route('departements.index')->with('success', 'Departemen berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat terhubung ke server backend.');
        }
    }
}