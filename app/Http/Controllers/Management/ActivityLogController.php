<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $project->load('company');

        $query = $project->activityLogs()->with('user');

        // Filter Modul
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter Aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Pencarian Kata Kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('management.projects.logs.index', compact('project', 'logs'));
    }
}