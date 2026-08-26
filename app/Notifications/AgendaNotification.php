<?php

namespace App\Notifications;

use App\Models\ProjectAgenda;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AgendaNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ProjectAgenda $agenda,
        public string $type
    ) {
        $this->agenda->load('project');
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
            'type' => 'agenda',
            'action_type' => $this->type,
            'agenda_id' => $this->agenda->id,
            'project_id' => $this->agenda->project_id,
            'project_name' => $this->agenda->project->name ?? 'Project',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
        ];
    }

    protected function getTitle(): string
    {
        return match ($this->type) {
            'scheduled' => 'Agenda Kegiatan Baru',
            'updated' => 'Informasi Agenda Diperbarui',
            default => 'Pemberitahuan Agenda',
        };
    }

    protected function getMessage(): string
    {
        $projectName = $this->agenda->project->name ?? 'Project';
        $formattedDate = date('d M Y', strtotime($this->agenda->start_date));

        return match ($this->type) {
            'scheduled' => "Anda diundang ke agenda '{$this->agenda->title}' ({$this->agenda->category}) pada proyek {$projectName} untuk tanggal {$formattedDate}.",
            'updated' => "Informasi status/catatan untuk agenda '{$this->agenda->title}' pada proyek {$projectName} telah diperbarui menjadi '{$this->agenda->status}'.",
            default => "Terdapat pembaruan pada agenda '{$this->agenda->title}'.",
        };
    }
}
