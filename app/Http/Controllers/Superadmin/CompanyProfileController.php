<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyProfileController extends Controller
{
    public function index(): View
    {
        $companies = CompanyProfile::with('manager')->latest()->paginate(10);
        return view('superadmin.company.index', compact('companies'));
    }

    public function create(): View
    {
        // Ambil user dengan role management untuk ditugaskan
        $managers = User::where('role', 'management')->get();
        return view('superadmin.company.create', compact('managers'));
    }

public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'manager_id'         => ['nullable'], // Validasi nullable
            'company_name'       => ['required', 'string', 'max:255'],
            'tagline'            => ['nullable', 'string', 'max:255'],
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:50'],
            'address'            => ['nullable', 'string'],
            'about'              => ['nullable', 'string'],
            'vision'             => ['nullable', 'string'],
            'mission'            => ['nullable', 'string'],
            'logo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'social_media'       => ['nullable', 'array'],
            'dynamic_sections'   => ['nullable', 'array'],
            'dynamic_files.*'    => ['nullable', 'file', 'max:10240'],
        ]);

        // Pastikan jika string kosong ("") diubah menjadi null
        $validated['manager_id'] = $request->filled('manager_id') ? (int) $request->manager_id : null;

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('company', 'public');
        }

        $dynamicSections = $request->input('dynamic_sections', []);
        if ($request->hasFile('dynamic_files')) {
            foreach ($request->file('dynamic_files') as $index => $file) {
                if (isset($dynamicSections[$index])) {
                    $dynamicSections[$index]['file_url'] = $file->store('company/dynamic', 'public');
                }
            }
        }

        $validated['dynamic_sections'] = array_values($dynamicSections);
        $validated['social_media'] = array_values($request->input('social_media', []));

        $company = CompanyProfile::create($validated);

        return redirect()->route('superadmin.company.show', $company->id)
            ->with('success', 'Perusahaan baru berhasil didaftarkan dan ditugaskan.');
    }

    public function show(CompanyProfile $company): View
    {
        $company->load('manager');
        return view('superadmin.company.show', compact('company'));
    }

    public function edit(CompanyProfile $company): View
    {
        $managers = User::where('role', 'management')->get();
        return view('superadmin.company.edit', compact('company', 'managers'));
    }

public function update(Request $request, CompanyProfile $company): RedirectResponse
    {
        $validated = $request->validate([
            'manager_id'         => ['nullable'],
            'company_name'       => ['required', 'string', 'max:255'],
            'tagline'            => ['nullable', 'string', 'max:255'],
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:50'],
            'address'            => ['nullable', 'string'],
            'about'              => ['nullable', 'string'],
            'vision'             => ['nullable', 'string'],
            'mission'            => ['nullable', 'string'],
            'logo'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'banner'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'social_media'       => ['nullable', 'array'],
            'dynamic_sections'   => ['nullable', 'array'],
            'dynamic_files.*'    => ['nullable', 'file', 'max:10240'],
        ]);

        // Pastikan jika string kosong ("") diubah menjadi null
        $validated['manager_id'] = $request->filled('manager_id') ? (int) $request->manager_id : null;

        if ($request->hasFile('logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($company->banner && Storage::disk('public')->exists($company->banner)) {
                Storage::disk('public')->delete($company->banner);
            }
            $validated['banner'] = $request->file('banner')->store('company', 'public');
        }

        $dynamicSections = $request->input('dynamic_sections', []);
        if ($request->hasFile('dynamic_files')) {
            foreach ($request->file('dynamic_files') as $index => $file) {
                if (isset($dynamicSections[$index])) {
                    $dynamicSections[$index]['file_url'] = $file->store('company/dynamic', 'public');
                }
            }
        }

        if ($company->dynamic_sections) {
            foreach ($dynamicSections as $index => $section) {
                if (empty($section['file_url']) && isset($company->dynamic_sections[$index]['file_url'])) {
                    $dynamicSections[$index]['file_url'] = $company->dynamic_sections[$index]['file_url'];
                }
            }
        }

        $validated['dynamic_sections'] = array_values($dynamicSections);
        $validated['social_media'] = array_values($request->input('social_media', []));

        $company->update($validated);

        return redirect()->route('superadmin.company.show', $company->id)
            ->with('success', 'Profil Perusahaan & Pengelola berhasil diperbarui.');
    }

    public function destroy(CompanyProfile $company): RedirectResponse
    {
        if ($company->logo && Storage::disk('public')->exists($company->logo)) {
            Storage::disk('public')->delete($company->logo);
        }
        if ($company->banner && Storage::disk('public')->exists($company->banner)) {
            Storage::disk('public')->delete($company->banner);
        }

        $company->delete();

        return redirect()->route('superadmin.company.index')->with('success', 'Perusahaan berhasil dihapus.');
    }
}
