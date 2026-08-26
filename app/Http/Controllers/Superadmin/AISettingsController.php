<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AISetting;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AISettingsController extends Controller
{
    /**
     * Tampilkan halaman daftar AI settings milik user aktif
     */
    public function index(): View
    {
        if (session()->has('demo_track_id')) {
            $settings = collect();

            return view('superadmin.ai-settings.index', compact('settings'));
        }

        $settings = AISetting::where('user_id', Auth::id())->latest()->get();

        return view('superadmin.ai-settings.index', compact('settings'));
    }

    /**
     * Simpan konfigurasi provider AI baru untuk user aktif
     */
    public function store(Request $request): RedirectResponse
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Aksi dinonaktifkan di akun demo untuk menjaga keamanan kredensial.');
        }

        $request->validate([
            'provider' => ['required', 'string', 'in:openai,gemini,openrouter'],
            'name' => ['required', 'string', 'max:100'],
            'api_key' => ['required', 'string'],
            'model' => ['required', 'string'],
            'base_url' => ['nullable', 'url'],
            'settings' => ['nullable', 'array'],
        ]);

        $data = $request->only(['provider', 'name', 'api_key', 'model', 'base_url', 'settings']);
        $data['user_id'] = Auth::id();

        // Buat set default fallback jika diset
        if ($request->has('enable_fallback')) {
            $data['settings']['enable_fallback'] = true;
            $data['settings']['fallback_provider'] = $request->input('fallback_provider');
        } else {
            $data['settings']['enable_fallback'] = false;
        }

        AISetting::create($data);

        return redirect()->route('ai-settings.index')
            ->with('success', 'Konfigurasi AI Provider pribadi berhasil ditambahkan.');
    }

    /**
     * Perbarui konfigurasi AI Provider milik user aktif
     */
    public function update(Request $request, AISetting $aiSetting): RedirectResponse
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Aksi dinonaktifkan di akun demo untuk menjaga keamanan kredensial.');
        }

        if ($aiSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'api_key' => ['nullable', 'string'],
            'model' => ['required', 'string'],
            'base_url' => ['nullable', 'url'],
            'settings' => ['nullable', 'array'],
        ]);

        $data = $request->only(['name', 'model', 'base_url', 'settings']);

        // Update API Key hanya jika user memasukkan input baru
        if ($request->filled('api_key') && $request->input('api_key') !== '••••••••') {
            $data['api_key'] = $request->input('api_key');
        }

        // Set fallback configuration
        $extraSettings = $aiSetting->settings ?? [];
        if ($request->has('enable_fallback')) {
            $extraSettings['enable_fallback'] = true;
            $extraSettings['fallback_provider'] = $request->input('fallback_provider');
        } else {
            $extraSettings['enable_fallback'] = false;
        }
        $data['settings'] = $extraSettings;

        $aiSetting->update($data);

        return redirect()->route('ai-settings.index')
            ->with('success', 'Konfigurasi AI Provider pribadi berhasil diperbarui.');
    }

    /**
     * Hapus konfigurasi AI milik user aktif
     */
    public function destroy(AISetting $aiSetting): RedirectResponse
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Aksi dinonaktifkan di akun demo untuk menjaga keamanan kredensial.');
        }

        if ($aiSetting->user_id !== Auth::id()) {
            abort(403);
        }

        $aiSetting->delete();

        return redirect()->route('ai-settings.index')
            ->with('success', 'Konfigurasi AI Provider pribadi berhasil dihapus.');
    }

    /**
     * Aktifkan AI Provider terpilih milik user aktif (Nonaktifkan yang lain)
     */
    public function activate(AISetting $aiSetting): RedirectResponse
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Aksi dinonaktifkan di akun demo untuk menjaga keamanan kredensial.');
        }

        if ($aiSetting->user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($aiSetting) {
            // Set all other providers of this user to inactive
            AISetting::where('user_id', Auth::id())
                ->where('id', '!=', $aiSetting->id)
                ->update(['is_active' => false]);

            // Set the selected provider to active
            $aiSetting->update(['is_active' => true]);
        });

        return redirect()->route('ai-settings.index')
            ->with('success', "AI Provider [{$aiSetting->name}] berhasil diaktifkan.");
    }

    /**
     * Tes koneksi ke AI provider milik user aktif
     */
    public function test(Request $request): JsonResponse
    {
        if (session()->has('demo_track_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur uji coba koneksi dinonaktifkan untuk akun demo.',
            ], 403);
        }

        $request->validate([
            'id' => ['nullable', 'integer'],
            'provider' => ['required', 'string', 'in:openai,gemini,openrouter'],
            'model' => ['required', 'string'],
            'api_key' => ['nullable', 'string'],
            'base_url' => ['nullable', 'url'],
        ]);

        $apiKey = $request->input('api_key');

        // Jika API Key placeholder, ambil dari existing record di database
        if ((! $apiKey || $apiKey === '••••••••') && $request->filled('id')) {
            $existing = AISetting::find($request->input('id'));
            if ($existing) {
                if ($existing->user_id !== Auth::id()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                $apiKey = $existing->api_key;
            }
        }

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API Key wajib diisi untuk melakukan pengujian.',
            ], 422);
        }

        try {
            // Buat instance temporer dari adapter
            $tempSetting = new AISetting([
                'provider' => $request->input('provider'),
                'model' => $request->input('model'),
                'base_url' => $request->input('base_url'),
            ]);
            // bypass encryption cast untuk instansiasi temporer
            $tempSetting->api_key = $apiKey;

            $aiService = app(AIService::class);
            $providerInstance = $aiService->makeProvider($tempSetting);

            $connected = $providerInstance->validateConnection();

            if ($connected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Koneksi berhasil terhubung.',
                    'provider' => $request->input('provider'),
                    'model' => $request->input('model'),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Koneksi gagal. Periksa kembali API Key atau Model ID.',
            ], 400);

        } catch (\Exception $e) {
            Log::error('AI settings test connection failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung: '.$e->getMessage(),
            ], 500);
        }
    }
}
