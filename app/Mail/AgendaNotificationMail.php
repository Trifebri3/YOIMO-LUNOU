<?php

namespace App\Mail;

use App\Models\ProjectAgenda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AgendaNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agenda;

    public $type; // 'scheduled', 'updated', or 'reminder'

    public function __construct(ProjectAgenda $agenda, string $type)
    {
        $this->agenda = $agenda;
        $this->type = $type;
    }

    public function build()
    {
        $subjectMap = [
            'scheduled' => 'Jadwal Agenda Baru: '.$this->agenda->title,
            'updated' => 'Pembaruan Jadwal Agenda: '.$this->agenda->title,
            'reminder' => 'PENGINGAT JADWAL: Agenda "'.$this->agenda->title.'" akan segera dimulai!',
        ];

        return $this->subject($subjectMap[$this->type] ?? 'Notifikasi Agenda Yoimo')
            ->html($this->renderHtmlContent());
    }

    private function renderHtmlContent(): string
    {
        $actionText = $this->type === 'scheduled' ? 'telah dijadwalkan' : ($this->type === 'updated' ? 'telah diperbarui' : 'akan segera berlangsung');
        $startDate = date('d M Y', strtotime($this->agenda->start_date));
        $startTime = $this->agenda->start_time ? date('H:i', strtotime($this->agenda->start_time)) : 'Belum Ditentukan';
        $location = $this->agenda->location_type === 'online'
            ? "Online (Link: <a href='{$this->agenda->meeting_url}'>{$this->agenda->meeting_url}</a>)"
            : ($this->agenda->location_address ?: 'Offline');

        return "
        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px;'>
            <div style='text-align: center; margin-bottom: 20px;'>
                <h1 style='color: #10b981; margin: 0;'>Yoimo Workspace</h1>
                <p style='color: #64748b; margin: 5px 0 0;'>Notifikasi Sistem LUNOU</p>
            </div>
            <div style='background-color: #f0fdf4; padding: 20px; border-radius: 12px; margin-bottom: 20px;'>
                <p style='font-size: 14px; color: #1e293b; line-height: 1.6;'>
                    Halo, <strong>Jadwal agenda baru {$actionText}</strong> pada proyek <strong>".($this->agenda->project->name ?? 'Project')."</strong>.
                </p>
                <table style='width: 100%; font-size: 13px; border-collapse: collapse; margin-top: 15px;'>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold; width: 30%;'>Nama Agenda:</td>
                        <td style='padding: 6px 0; color: #1e293b; font-weight: bold;'>{$this->agenda->title}</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Kategori:</td>
                        <td style='padding: 6px 0; color: #10b981; font-weight: bold;'>{$this->agenda->category}</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Tanggal:</td>
                        <td style='padding: 6px 0; color: #1e293b;'>{$startDate}</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Waktu / Jam:</td>
                        <td style='padding: 6px 0; color: #1e293b;'>{$startTime} WIB</td>
                    </tr>
                    <tr>
                        <td style='padding: 6px 0; color: #64748b; font-weight: bold;'>Lokasi/Link:</td>
                        <td style='padding: 6px 0; color: #4f46e5;'>{$location}</td>
                    </tr>
                </table>
            </div>
            <div style='text-align: center;'>
                <a href='".url('/management/projects/'.$this->agenda->project_id.'/agendas')."' style='display: inline-block; background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 13px;'>Lihat Kalender Agenda</a>
            </div>
            <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
            <p style='font-size: 11px; color: #94a3b8; text-align: center; margin: 0;'>
                Email ini dikirim otomatis oleh LUNOU AI Engine. Harap tidak membalas email ini.
            </p>
        </div>";
    }
}
