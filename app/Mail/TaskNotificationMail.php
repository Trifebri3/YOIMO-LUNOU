<?php

namespace App\Mail;

use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $type; // 'created', 'updated', or 'reminder'

    public function __construct(ProjectTask $task, string $type)
    {
        $this->task = $task;
        $this->type = $type;
    }

    public function build()
    {
        $subjectMap = [
            'created' => 'Tugas Baru Ditugaskan: ' . $this->task->title,
            'updated' => 'Pembaruan Tugas: ' . $this->task->title,
            'reminder' => 'PENGINGAT DEADLINE: Tugas "' . $this->task->title . '" mendekati tenggat waktu!'
        ];

        return $this->subject($subjectMap[$this->type] ?? 'Notifikasi Tugas Yoimo')
                    ->html($this->renderHtmlContent());
    }

    private function renderHtmlContent(): string
    {
        $actionText = $this->type === 'created' ? 'telah ditugaskan kepada Anda' : ($this->type === 'updated' ? 'telah diperbarui' : 'akan segera jatuh tempo');
        $dueDate = $this->task->due_date ? date('d M Y', strtotime($this->task->due_date)) : 'Tanpa Tenggat';
        $priorityBadgeColor = [
            'Low' => '#3b82f6',
            'Medium' => '#f59e0b',
            'High' => '#ef4444',
            'Urgent' => '#7c3aed'
        ][$this->task->priority] ?? '#64748b';

        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h1 style='color: #4f46e5; margin: 0;'>Yoimo Workspace</h1>
                <p style='color: #64748b; margin: 5px 0 0;'>Notifikasi Sistem LUNOU</p>
            </div>
            <div style='background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-bottom: 20px;'>
                <p style='font-size: 14px; color: #334155; line-height: 1.6;'>
                    Halo, <strong>Tugas Anda {$actionText}</strong> pada proyek <strong>" . ($this->task->project->name ?? 'Project') . "</strong>.
                </p>
                <table style='width: 100%; font-size: 13px; border-collapse: collapse; margin-top: 15px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold; width: 30%;'>Judul Tugas:</td>
                        <td style='padding: 6px 0; color: #1e293b; font-weight: bold;'>{$this->task->title}</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Prioritas:</td>
                        <td style='padding: 6px 0;'><span style='background-color: {$priorityBadgeColor}; color: white; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;'>{$this->task->priority}</span></td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Tenggat Waktu:</td>
                        <td style='padding: 6px 0; color: #ef4444; font-weight: bold;'>{$dueDate}</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Status:</td>
                        <td style='padding: 6px 0; color: #475569;'>{$this->task->status}</td>
                    </tr>
                </table>
            </div>
            <div style='text-align: center;'>
                <a href='" . url('/user/projects/' . $this->task->project_id . '?tab=tasks') . "' style='display: inline-block; background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px;'>Buka Halaman Tugas</a>
            </div>
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
            <p style='font-size: 11px; color: #94a3b8; text-align: center; margin: 0;'>
                Email ini dikirim otomatis oleh LUNOU AI Engine. Harap tidak membalas email ini.
            </p>
        </div>";
    }
}
