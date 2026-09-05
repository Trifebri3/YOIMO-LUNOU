@extends('superadmin.layouts.app')

@section('title', 'Konfigurasi Notifikasi Email & WA')

@section('content')
<div class="space-y-8">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Konfigurasi Notifikasi & WA</h1>
            <p class="text-xs font-semibold text-slate-400 mt-1">
                Atur parameter SMTP server Hostinger dan token WhatsApp Fonnte untuk notifikasi otomatis.
            </p>
        </div>
    </div>

    <!-- Main Setting Grid -->
    <form action="{{ route('superadmin.notification-settings.update') }}" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        @csrf
        
        <!-- Left Side: SMTP Mail Settings (7 Cols) -->
        <div class="xl:col-span-7 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-50 pb-4">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">SMTP Email Server (Hostinger)</h2>
                        <p class="text-[10px] text-slate-400">Konfigurasi port, enkripsi, dan credential email pengirim.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- SMTP Host -->
                    <div class="space-y-1.5">
                        <label for="mail_host" class="text-xs font-bold text-slate-500 uppercase tracking-wider">SMTP Host</label>
                        <input type="text" id="mail_host" name="mail_host" value="{{ old('mail_host', $mailConfig['host']) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>

                    <!-- SMTP Port -->
                    <div class="space-y-1.5">
                        <label for="mail_port" class="text-xs font-bold text-slate-500 uppercase tracking-wider">SMTP Port</label>
                        <input type="text" id="mail_port" name="mail_port" value="{{ old('mail_port', $mailConfig['port']) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- SMTP Username -->
                    <div class="space-y-1.5">
                        <label for="mail_username" class="text-xs font-bold text-slate-500 uppercase tracking-wider">SMTP Username</label>
                        <input type="text" id="mail_username" name="mail_username" value="{{ old('mail_username', $mailConfig['username']) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>

                    <!-- SMTP Password -->
                    <div class="space-y-1.5">
                        <label for="mail_password" class="text-xs font-bold text-slate-500 uppercase tracking-wider">SMTP Password</label>
                        <input type="password" id="mail_password" name="mail_password" placeholder="••••••••"
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- SMTP Encryption -->
                    <div class="space-y-1.5">
                        <label for="mail_encryption" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Encryption Type</label>
                        <select id="mail_encryption" name="mail_encryption" required
                                class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                            <option value="ssl" {{ old('mail_encryption', $mailConfig['encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="tls" {{ old('mail_encryption', $mailConfig['encryption']) === 'tls' ? 'selected' : '' }}>TLS / STARTTLS</option>
                            <option value="null" {{ old('mail_encryption', $mailConfig['encryption']) === 'null' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <!-- From Address -->
                    <div class="space-y-1.5">
                        <label for="mail_from_address" class="text-xs font-bold text-slate-500 uppercase tracking-wider">From Email Address</label>
                        <input type="email" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $mailConfig['from_address']) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>

                    <!-- From Name -->
                    <div class="space-y-1.5">
                        <label for="mail_from_name" class="text-xs font-bold text-slate-500 uppercase tracking-wider">From Sender Name</label>
                        <input type="text" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $mailConfig['from_name']) }}" required
                               class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-50">
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors rounded-xl shadow-sm font-sans cursor-pointer">
                        Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Side: WhatsApp & Fonnte Settings (5 Cols) -->
        <div class="xl:col-span-5 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-50 pb-4">
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">WhatsApp (Fonnte)</h2>
                        <p class="text-[10px] text-slate-400">Konfigurasi API token perangkat dan target fallback utama.</p>
                    </div>
                </div>

                <!-- Fonnte Token -->
                <div class="space-y-1.5">
                    <label for="fonnte_token" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Fonnte Device Token</label>
                    <input type="text" id="fonnte_token" name="fonnte_token" value="{{ old('fonnte_token', $whatsappConfig['token']) }}" required
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                </div>

                <!-- Fallback Target -->
                <div class="space-y-1.5">
                    <label for="fonnte_default_target" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Default Target Number</label>
                    <input type="text" id="fonnte_default_target" name="fonnte_default_target" value="{{ old('fonnte_default_target', $whatsappConfig['default_target']) }}" required
                           class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-50">
                    <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors rounded-xl shadow-sm font-sans cursor-pointer">
                        Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Testing Interface Panel Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Test SMTP Box -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-50 text-amber-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 font-sans">Uji Coba Pengiriman Email</h3>
                    <p class="text-[10px] text-slate-400">Kirim email kosong untuk menguji kredensial SMTP.</p>
                </div>
            </div>

            <form action="{{ route('superadmin.notification-settings.test-email') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="email" name="test_email_address" placeholder="tujuan@email.com" required
                       class="flex-1 px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 transition-colors rounded-xl shadow-sm whitespace-nowrap cursor-pointer">
                    Kirim Test Email
                </button>
            </form>
        </div>

        <!-- Test WhatsApp Box -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-800 font-sans">Uji Coba Pengiriman WhatsApp</h3>
                    <p class="text-[10px] text-slate-400">Kirim pesan demonstrasi ke nomor WhatsApp Fonnte Anda.</p>
                </div>
            </div>

            <form action="{{ route('superadmin.notification-settings.test-whatsapp') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="test_whatsapp_number" placeholder="628xxxxxxxxxx" required
                       class="flex-1 px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all font-semibold text-slate-700 font-sans">
                <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-indigo-500 hover:bg-indigo-600 transition-colors rounded-xl shadow-sm whitespace-nowrap cursor-pointer font-sans">
                    Kirim Test WA
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
