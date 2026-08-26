<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $demoTracks = DB::table('demo_tracks')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('superadmin.dashboard', compact('demoTracks'));
    }

    public function notificationSettings(): View
    {
        // Read current configurations from env/config
        $mailConfig = [
            'mailer' => env('MAIL_MAILER', 'smtp'),
            'host' => env('MAIL_HOST', ''),
            'port' => env('MAIL_PORT', '465'),
            'username' => env('MAIL_USERNAME', ''),
            'password' => env('MAIL_PASSWORD', ''),
            'encryption' => env('MAIL_ENCRYPTION', 'ssl'),
            'from_address' => env('MAIL_FROM_ADDRESS', ''),
            'from_name' => env('MAIL_FROM_NAME', ''),
        ];

        $whatsappConfig = [
            'token' => env('FONNTE_TOKEN', ''),
            'default_target' => env('FONNTE_DEFAULT_TARGET', ''),
        ];

        if (session()->has('demo_track_id')) {
            $mailConfig['password'] = '••••••••';
            $mailConfig['username'] = '••••••••';
            $whatsappConfig['token'] = '••••••••';
        }

        return view('superadmin.notification_settings', compact('mailConfig', 'whatsappConfig'));
    }

    public function updateNotificationSettings(Request $request)
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Konfigurasi kredensial dinonaktifkan di akun demo untuk alasan keamanan.');
        }

        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'required|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|in:ssl,tls,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'fonnte_token' => 'required|string',
            'fonnte_default_target' => 'required|string',
        ]);

        $envData = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => $request->mail_host,
            'MAIL_PORT' => $request->mail_port,
            'MAIL_USERNAME' => $request->mail_username,
            'MAIL_ENCRYPTION' => $request->mail_encryption === 'null' ? 'null' : $request->mail_encryption,
            'MAIL_FROM_ADDRESS' => $request->mail_from_address,
            'MAIL_FROM_NAME' => '"'.$request->mail_from_name.'"',
            'FONNTE_TOKEN' => $request->fonnte_token,
            'FONNTE_DEFAULT_TARGET' => $request->fonnte_default_target,
        ];

        if ($request->filled('mail_password')) {
            $envData['MAIL_PASSWORD'] = $request->mail_password;
        }

        $this->updateEnv($envData);

        // Clear config cache to apply immediately
        Artisan::call('config:clear');

        return back()->with('success', 'Konfigurasi Email & WhatsApp berhasil disimpan dan diperbarui di sistem.');
    }

    public function testEmail(Request $request)
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Fitur uji coba dinonaktifkan di akun demo.');
        }

        $request->validate([
            'test_email_address' => 'required|email',
        ]);

        try {
            Mail::raw('Halo! Ini adalah email uji coba pengetesan dari panel Super Admin Yoimo Workspace. Sistem SMTP Hostinger/Email Anda berfungsi 100%!', function ($message) use ($request) {
                $message->to($request->test_email_address)
                    ->subject('Uji Coba Pengiriman SMTP Yoimo');
            });

            return back()->with('success', 'Email uji coba berhasil dikirim ke '.$request->test_email_address.'. Silakan periksa kotak masuk/spam Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email uji coba: '.$e->getMessage());
        }
    }

    public function testWhatsapp(Request $request)
    {
        if (session()->has('demo_track_id')) {
            return back()->with('error', 'Fitur uji coba dinonaktifkan di akun demo.');
        }

        $request->validate([
            'test_whatsapp_number' => 'required|string',
        ]);

        $success = WhatsAppService::send($request->test_whatsapp_number, "🤖 *UJI COBA NOTIFIKASI WHATSAPP*\n\nHalo! Ini adalah pesan uji coba dari panel Super Admin Yoimo Workspace. Koneksi API Fonnte Anda berfungsi 100%!");

        if ($success) {
            return back()->with('success', 'Pesan WhatsApp uji coba berhasil dikirim ke nomor '.$request->test_whatsapp_number.'.');
        } else {
            return back()->with('error', 'Gagal mengirim pesan WhatsApp uji coba. Silakan cek token Fonnte Anda di log.');
        }
    }

    protected function updateEnv(array $data)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            foreach ($data as $key => $value) {
                if (preg_match("/^{$key}=.*/m", $content)) {
                    $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
                } else {
                    $content .= "\n{$key}={$value}";
                }
            }
            file_put_contents($path, $content);
        }
    }
}
