<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use Illuminate\View\View;

class PublicTaskController extends Controller
{
    /**
     * Tampilkan Halaman Verifikasi Publik Penyelesaian Tugas
     */
    public function show(int $id): View
    {
        $task = ProjectTask::withoutGlobalScopes()->with(['project.company', 'assignee', 'creator', 'roadmap'])->findOrFail($id);

        return view('public.task.share', compact('task'));
    }
}
