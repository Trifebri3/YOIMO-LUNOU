<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $project->load(['repositoryDocuments.creator', 'company']);

        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = $project->repositoryDocuments();

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $documents = $query->get();

        return view('management.projects.documents.index', compact('project', 'documents', 'selectedCategory', 'search'));
    }

    public function show(Project $project, ProjectDocument $document): View
    {
        $readers = $document->readers_log ?? [];
        $userId = Auth::id();
        $hasRead = collect($readers)->contains('user_id', $userId);

        if (!$hasRead) {
            $readers[] = [
                'user_id' => $userId,
                'name'    => Auth::user()->name,
                'read_at' => now()->format('Y-m-d H:i:s')
            ];
            $document->update(['readers_log' => $readers]);

            // Catat Audit Log saat user pertama kali membaca dokumen
            ProjectActivityLog::record($project->id, 'Document', 'READ', "Membuka dan membaca dokumen panduan: '{$document->title}'");
        }

        return view('management.projects.documents.show', compact('project', 'document'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string'],
            'doc_type'     => ['required', 'in:file,link,article'],
            'is_mandatory' => ['nullable', 'boolean'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'content'      => ['nullable', 'string'],
            'doc_file'     => ['nullable', 'file', 'max:25600'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['is_mandatory'] = $request->has('is_mandatory');

        if ($request->hasFile('doc_file')) {
            $file = $request->file('doc_file');
            $validated['file_path'] = $file->store('project_repository', 'public');
            $validated['file_name_original'] = $file->getClientOriginalName();
            $validated['file_size'] = round($file->getSize() / 1024, 1) . ' KB';
        }

        ProjectDocument::create($validated);

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Document', 'CREATE', "Mengunggah dokumen repositori: '{$validated['title']}' ({$validated['category']})");

        return redirect()->route('management.projects.documents.index', $project->id)
            ->with('success', 'Dokumen repositori berhasil diunggah ke perpustakaan proyek.');
    }

    public function destroy(Project $project, ProjectDocument $document): RedirectResponse
    {
        $docTitle = $document->title;

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        // Catat Audit Log
        ProjectActivityLog::record($project->id, 'Document', 'DELETE', "Menghapus dokumen repositori: '{$docTitle}'");

        return redirect()->route('management.projects.documents.index', $project->id)
            ->with('success', 'Dokumen berhasil dihapus dari repositori.');
    }
}