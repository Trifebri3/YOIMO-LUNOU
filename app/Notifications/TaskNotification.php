<?php

namespace App\Notifications;

use App\Models\ProjectTask;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ProjectTask $task,
        public string $type
    ) {
        $this->task->load('project');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task',
            'action_type' => $this->type,
            'task_id' => $this->task->id,
            'project_id' => $this->task->project_id,
            'project_name' => $this->task->project->name ?? 'Project',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
        ];
    }

    protected function getTitle(): string
    {
        return match ($this->type) {
            'created' => 'Tugas Baru Ditugaskan',
            'claimed' => 'Tugas Telah Diklaim',
            'updated' => 'Status Tugas Diperbarui',
            'submitted' => 'Tugas Butuh Review',
            default => 'Pemberitahuan Tugas',
        };
    }

    protected function getMessage(): string
    {
        $projectName = $this->task->project->name ?? 'Project';

        return match ($this->type) {
            'created' => "Tugas '{$this->task->title}' telah ditugaskan kepada Anda pada proyek {$projectName}.",
            'claimed' => "PIC telah mengambil tugas terbuka '{$this->task->title}' pada proyek {$projectName}.",
            'updated' => "Status tugas '{$this->task->title}' pada proyek {$projectName} telah diubah menjadi '{$this->task->status}'.",
            'submitted' => "Tugas '{$this->task->title}' pada proyek {$projectName} telah selesai dan membutuhkan review Anda.",
            default => "Terdapat pembaruan pada tugas '{$this->task->title}'.",
        };
    }
}
