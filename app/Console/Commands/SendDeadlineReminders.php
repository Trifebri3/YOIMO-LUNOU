<?php

namespace App\Console\Commands;

use App\Mail\AgendaNotificationMail;
use App\Mail\TaskNotificationMail;
use App\Models\ProjectAgenda;
use App\Models\ProjectTask;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDeadlineReminders extends Command
{
    protected $signature = 'send:deadline-reminders';

    protected $description = 'Kirim notifikasi pengingat email untuk tugas mendekati deadline dan agenda terdekat.';

    public function handle()
    {
        $this->info('Memulai pemindaian tenggat waktu tugas & agenda...');

        // 1. Scan Tasks (due tomorrow, status not Completed)
        $tomorrowDate = now()->addDay()->toDateString();
        $tasks = ProjectTask::where('due_date', $tomorrowDate)
            ->where('status', '!=', 'Completed')
            ->with('assignee', 'project')
            ->get();

        $this->info("Menemukan {$tasks->count()} tugas mendekati deadline besok.");

        foreach ($tasks as $task) {
            if ($task->assignee) {
                if ($task->assignee->email) {
                    try {
                        Mail::to($task->assignee->email)->send(new TaskNotificationMail($task, 'reminder'));
                        $this->info("Pengingat tugas dikirim ke: {$task->assignee->email}");
                    } catch (\Exception $e) {
                        Log::error('Failed to send Task deadline reminder: '.$e->getMessage());
                    }
                }

                // WhatsApp H-1 Task Reminder
                $msg = "⏰ *PENGINGAT DEADLINE (H-1)*\n\n"
                    ."Halo, tugas Anda mendekati batas tenggat waktu besok:\n"
                    .'• *Proyek*: '.($task->project->name ?? 'Project')."\n"
                    ."• *Tugas*: {$task->title}\n"
                    ."• *Prioritas*: {$task->priority}\n"
                    .'• *Tenggat*: '.($task->due_date ? date('d M Y', strtotime($task->due_date)) : 'Besok')."\n\n"
                    .'Harap segera selesaikan laporan kerja Anda di dashboard Yoimo!';
                WhatsAppService::send($task->assignee->phone, $msg);
            }
        }

        // 2. Scan Agendas (starting tomorrow)
        $agendas = ProjectAgenda::where('start_date', $tomorrowDate)
            ->where('status', 'Scheduled')
            ->with('project')
            ->get();

        $this->info("Menemukan {$agendas->count()} agenda besok.");

        foreach ($agendas as $agenda) {
            $attendeeIds = $agenda->attendee_ids ?? [];
            if (! empty($attendeeIds)) {
                $users = User::whereIn('id', $attendeeIds)->get();
                foreach ($users as $u) {
                    if ($u->email) {
                        try {
                            Mail::to($u->email)->send(new AgendaNotificationMail($agenda, 'reminder'));
                            $this->info("Pengingat agenda dikirim ke: {$u->email}");
                        } catch (\Exception $e) {
                            Log::error('Failed to send Agenda reminder: '.$e->getMessage());
                        }
                    }

                    // WhatsApp H-1 Agenda Reminder
                    $formattedDate = date('d M Y', strtotime($agenda->start_date));
                    $formattedTime = $agenda->start_time ? date('H:i', strtotime($agenda->start_time)) : 'Belum Ditentukan';
                    $location = $agenda->location_type === 'online'
                        ? "Virtual Online (Link: {$agenda->meeting_url})"
                        : ($agenda->location_address ?: 'Offline');

                    $msg = "⏰ *PENGINGAT AGENDA BESOK (H-1)*\n\n"
                        ."Halo, agenda Anda dijadwalkan berlangsung besok:\n"
                        ."• *Agenda*: {$agenda->title}\n"
                        .'• *Proyek*: '.($agenda->project->name ?? 'Project')."\n"
                        ."• *Waktu*: {$formattedDate} jam {$formattedTime} WIB\n"
                        ."• *Lokasi*: {$location}\n\n"
                        .'Sampai jumpa di lokasi rapat!';
                    WhatsAppService::send($u->phone, $msg);
                }
            }
        }

        $this->info('Selesai mengirim pengingat email.');
    }
}
