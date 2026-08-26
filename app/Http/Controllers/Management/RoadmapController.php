<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectRoadmap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoadmapController extends Controller
{
    public function index(Project $project): View
    {
        $project->load(['roadmaps', 'company']);
        return view('management.projects.roadmaps.index', compact('project'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
            'status'       => ['required', 'in:Pending,In Progress,Completed'],
            'objectives'   => ['nullable', 'array'],
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        // 1. Validasi Batas Tanggal Project (Tidak boleh sebelum start_date atau lewat deadline project)
        if ($project->start_date && $start < $project->start_date->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'start_date' => 'Tanggal mulai linimasa tidak boleh mendahului tanggal mulai project (' . $project->start_date->format('d M Y') . ').'
            ]);
        }

        if ($project->deadline && $end > $project->deadline->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'end_date' => 'Tanggal selesai linimasa tidak boleh melebihi deadline akhir project (' . $project->deadline->format('d M Y') . ').'
            ]);
        }

        // 2. Validasi Anti-Tumpang Tindih (No Overlapping with Existing Roadmaps)
        $overlap = ProjectRoadmap::where('project_id', $project->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => 'Jadwal linimasa bentrok / tumpang tindih dengan fase linimasa lain yang sudah ada di proyek ini.'
            ]);
        }

        // Siapkan Target Ketercapaian
        $objectives = [];
        if ($request->filled('objectives')) {
            foreach ($request->objectives as $target) {
                if (!empty(trim($target))) {
                    $objectives[] = [
                        'target'      => trim($target),
                        'is_achieved' => false
                    ];
                }
            }
        }

        ProjectRoadmap::create([
            'project_id'          => $project->id,
            'title'               => $request->title,
            'description'         => $request->description,
            'start_date'          => $start,
            'end_date'            => $end,
            'status'              => $request->status,
            'progress_percentage' => 0,
            'objectives'          => $objectives,
        ]);

        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa / roadmap baru berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, ProjectRoadmap $roadmap): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'start_date'   => ['required', 'date'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
            'status'       => ['required', 'in:Pending,In Progress,Completed'],
            'objectives'   => ['nullable', 'array'],
        ]);

        $start = $request->start_date;
        $end = $request->end_date;

        // Validasi Rentang Project
        if ($project->start_date && $start < $project->start_date->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'start_date' => 'Tanggal mulai linimasa tidak boleh mendahului tanggal mulai project (' . $project->start_date->format('d M Y') . ').'
            ]);
        }

        if ($project->deadline && $end > $project->deadline->format('Y-m-d')) {
            return back()->withInput()->withErrors([
                'end_date' => 'Tanggal selesai linimasa tidak boleh melebihi deadline project (' . $project->deadline->format('d M Y') . ').'
            ]);
        }

        // Validasi Overlapping (Kecualikan ID roadmap saat ini)
        $overlap = ProjectRoadmap::where('project_id', $project->id)
            ->where('id', '!=', $roadmap->id)
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                      });
            })
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'start_date' => 'Jadwal linimasa bentrok / tumpang tindih dengan fase linimasa lain yang sudah ada.'
            ]);
        }

        // Proses Target & Status Ketercapaian
        $objectives = [];
        $achievedCount = 0;
        if ($request->filled('objectives')) {
            $achievedInputs = $request->input('achieved_status', []);
            foreach ($request->objectives as $idx => $targetText) {
                if (!empty(trim($targetText))) {
                    $isAchieved = isset($achievedInputs[$idx]) && $achievedInputs[$idx] == '1';
                    if ($isAchieved) $achievedCount++;
                    
                    $objectives[] = [
                        'target'      => trim($targetText),
                        'is_achieved' => $isAchieved
                    ];
                }
            }
        }

        // Hitung persentase ketercapaian otomatis
        $totalObjectives = count($objectives);
        $progress = $totalObjectives > 0 ? round(($achievedCount / $totalObjectives) * 100) : 0;

        $roadmap->update([
            'title'               => $request->title,
            'description'         => $request->description,
            'start_date'          => $start,
            'end_date'            => $end,
            'status'              => $request->status,
            'progress_percentage' => $progress,
            'objectives'          => $objectives,
        ]);

        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa & target ketercapaian berhasil diperbarui.');
    }

    public function destroy(Project $project, ProjectRoadmap $roadmap): RedirectResponse
    {
        $roadmap->delete();
        return redirect()->route('management.projects.roadmaps.index', $project->id)
            ->with('success', 'Fase linimasa telah dihapus.');
    }
}