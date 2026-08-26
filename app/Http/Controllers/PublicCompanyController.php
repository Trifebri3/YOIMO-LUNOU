<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\View\View;

class PublicCompanyController extends Controller
{
    public function show(string $slug): View
    {
        $company = CompanyProfile::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('public.company.show', compact('company'));
    }
}