<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectActivityLog;
use App\Models\ProjectExpense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request, Project $project): View
    {
        $project->load(['expenses.uploader', 'company']);

        // Filter Kategori jika ada
        $selectedCategory = $request->query('category');
        $expensesQuery = $project->expenses();

        if ($selectedCategory) {
            $expensesQuery->where('category', $selectedCategory);
        }

        $expenses = $expensesQuery->get();

        // Hitung Metrik Finansial
        $totalBudget = $project->budget;
        $totalSpent = $project->expenses()->where('status', 'Approved')->sum('amount');
        $remainingBudget = $totalBudget - $totalSpent;
        $spentPercentage = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0;

        return view('management.projects.expenses.index', compact(
            'project',
            'expenses',
            'totalBudget',
            'totalSpent',
            'remainingBudget',
            'spentPercentage',
            'selectedCategory'
        ));
    }

    /**
     * Toggle Pengaktifan Transparansi Finansial Proyek oleh Management
     */
    public function toggleTransparency(Request $request, Project $project): RedirectResponse
    {
        $project->is_financial_transparent = !$project->is_financial_transparent;
        $project->save();

        $statusText = $project->is_financial_transparent ? 'diaktifkan (terbuka untuk seluruh tim)' : 'dinonaktifkan (disembunyikan dari tim)';

        // Catat Audit Log
        ProjectActivityLog::record(
            $project->id, 
            'Expense', 
            'TOGGLE', 
            "Mengubah transparansi laporan keuangan menjadi " . ($project->is_financial_transparent ? 'Aktif' : 'Nonaktif')
        );

        return back()->with('success', "Transparansi laporan belanja proyek berhasil {$statusText}.");
    }

    /**
     * Input Pengeluaran / Belanja Baru (Bisa oleh Finance atau Management)
     */
    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string'],
            'amount'       => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
            'receipt_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:10240'],
            'notes'        => ['nullable', 'string'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'Approved';

        if ($request->hasFile('receipt_file')) {
            $validated['receipt_file'] = $request->file('receipt_file')->store('expense_receipts', 'public');
        }

        ProjectExpense::create($validated);

        // Update nominal budget_spent di tabel projects secara otomatis
        $totalApproved = $project->expenses()->where('status', 'Approved')->sum('amount') + $validated['amount'];
        $project->update(['budget_spent' => $totalApproved]);

        // Catat Audit Log
        ProjectActivityLog::record(
            $project->id, 
            'Expense', 
            'CREATE', 
            "Mencatatkan pengeluaran belanja: '{$validated['title']}' sebesar Rp " . number_format($validated['amount'], 0, ',', '.')
        );

        return redirect()->route('management.projects.expenses.index', $project->id)
            ->with('success', 'Catatan pembelanjaan baru berhasil ditambahkan.');
    }

    public function destroy(Project $project, ProjectExpense $expense): RedirectResponse
    {
        $expenseTitle = $expense->title;
        $expenseAmount = $expense->amount;

        if ($expense->receipt_file && Storage::disk('public')->exists($expense->receipt_file)) {
            Storage::disk('public')->delete($expense->receipt_file);
        }

        $expense->delete();

        // Rekalkulasi budget terpakai
        $totalApproved = $project->expenses()->where('status', 'Approved')->sum('amount');
        $project->update(['budget_spent' => $totalApproved]);

        // Catat Audit Log
        ProjectActivityLog::record(
            $project->id, 
            'Expense', 
            'DELETE', 
            "Menghapus catatan belanja: '{$expenseTitle}' senilai Rp " . number_format($expenseAmount, 0, ',', '.')
        );

        return redirect()->route('management.projects.expenses.index', $project->id)
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }
}