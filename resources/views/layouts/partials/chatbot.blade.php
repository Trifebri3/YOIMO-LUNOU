@php
    $chatbotMessages = [];
    if (Auth::check()) {
        $chatbotMessages = \App\Models\ChatbotMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->take(30)
            ->get();
    }
@endphp

<!-- LUNOU Floating Mascot Chatbot Companion -->
<div id="lunou-chatbot-wrapper" class="fixed bottom-24 md:bottom-6 right-6 z-[9999] font-sans select-none pointer-events-none">
    
    <!-- Chatbot Window Popup Card -->
    <div id="lunou-chatbot-window" class="hidden pointer-events-auto w-76 sm:w-88 h-[400px] bg-white border border-slate-200/80 rounded-3xl shadow-2xl flex flex-col overflow-hidden mb-4 transition-all duration-300 transform translate-y-10 opacity-0">
        
        <!-- Header -->
        <div class="p-3.5 bg-indigo-600 text-white flex items-center justify-between shadow-sm shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/10 p-0.5 border border-white/20 shrink-0">
                    <img id="lunou-bot-header-avatar" src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-full h-full object-contain">
                </div>
                <div>
                    <h4 class="text-xs font-black tracking-wide">LUNOU Companion</h4>
                    <span id="lunou-bot-status" class="text-[9px] font-bold text-emerald-300 block">Sedia Membantu</span>
                </div>
            </div>
            <button type="button" onclick="toggleLunouChatbot()" class="text-white/80 hover:text-white font-bold text-lg p-1">&times;</button>
        </div>

        <!-- Scrollable Messages Feed -->
        <div id="lunou-bot-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50">
            <!-- Initial Greeting -->
            <div class="flex items-start gap-2">
                <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                <div class="max-w-[80%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                    <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">Halo! Aku LUNOU, maskot workspace-mu. Butuh bantuan memantau proyek, jadwal rapat, tugas aktifmu, atau sekadar butuh kata motivasi? Tanyakan saja padaku!</p>
                </div>
            </div>

            @foreach($chatbotMessages as $chat)
                @if($chat->role === 'user')
                    <div class="flex items-start gap-2 justify-end">
                        <div class="max-w-[80%] bg-indigo-600 text-white rounded-2xl rounded-tr-none p-3 shadow-xs">
                            <p class="text-[11px] leading-relaxed font-semibold">{!! nl2br(preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" class="text-indigo-200 hover:text-white font-black underline transition-all">$1</a>', e($chat->message))) !!}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-2 justify-start">
                        <img src="{{ asset('icon/11.png') }}" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                        <div class="max-w-[80%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                            <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">{!! nl2br(preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" class="text-indigo-600 hover:text-indigo-800 font-black underline transition-all">$1</a>', e($chat->message))) !!}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Input Footer -->
        <div class="p-3 bg-white border-t border-slate-100 shrink-0">
            <form id="lunou-bot-form" onsubmit="sendLunouQuery(event)" class="flex items-center gap-2">
                <button type="button" id="lunou-mic-btn" onclick="toggleSpeechRecognition()" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-slate-50 rounded-xl transition-all shrink-0" title="Kirim Pesan Suara (Voice to Text)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                    </svg>
                </button>
                <input type="text" id="lunou-bot-input" placeholder="Tanya LUNOU..." autocomplete="off"
                       class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none">
                <button type="submit" class="p-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition-all shrink-0">
                    <svg class="w-3.5 h-3.5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>

    </div>

    <!-- Floating Animated Mascot Trigger Button -->
    <div class="flex justify-end pointer-events-auto">
        <button type="button" onclick="toggleLunouChatbot()" 
                class="group relative w-16 h-16 bg-white border-2 border-indigo-200 hover:border-indigo-400 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center animate-bob"
                title="Tanya LUNOU!">
            <!-- Glowing Ring -->
            <span class="absolute inset-0 rounded-full border border-indigo-100 animate-ping opacity-75"></span>
            
            <img id="lunou-mascot-img" src="{{ asset('icon/11.png') }}" alt="LUNOU" 
                 class="w-12 h-12 object-contain transition-all duration-300 group-hover:scale-110">
        </button>
    </div>

</div>

<!-- Styles & Animations -->
<style>
    @keyframes bob {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bob {
        animation: bob 3s ease-in-out infinite;
    }
    
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .animate-heartbeat {
        animation: heartbeat 1s ease-in-out infinite;
    }
</style>

<!-- Mascot AI Chatbot Javascript Handler -->
<script>
    let isBotWindowOpen = false;
    let botRecognition = null;
    let isBotListening = false;

    function toggleSpeechRecognition() {
        const micBtn = document.getElementById('lunou-mic-btn');
        const input = document.getElementById('lunou-bot-input');

        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Maaf, browser Anda tidak mendukung fitur Voice-to-Text. Silakan gunakan Google Chrome, Safari, atau Microsoft Edge.");
            return;
        }

        if (isBotListening) {
            botRecognition.stop();
            return;
        }

        const SpeechRecognitionClass = window.SpeechRecognition || window.webkitSpeechRecognition;
        botRecognition = new SpeechRecognitionClass();
        botRecognition.lang = 'id-ID';
        botRecognition.continuous = false;
        botRecognition.interimResults = false;

        botRecognition.onstart = function() {
            isBotListening = true;
            micBtn.classList.remove('text-slate-400');
            micBtn.classList.add('text-rose-500', 'bg-rose-50', 'animate-pulse');
            input.placeholder = "Mendengarkan...";
        };

        botRecognition.onerror = function(event) {
            console.error("Speech recognition error", event.error);
            stopBotRecognition();
        };

        botRecognition.onend = function() {
            stopBotRecognition();
        };

        botRecognition.onresult = function(event) {
            const resultText = event.results[0][0].transcript;
            if (resultText) {
                input.value = resultText;
            }
        };

        botRecognition.start();
    }

    function stopBotRecognition() {
        isBotListening = false;
        const micBtn = document.getElementById('lunou-mic-btn');
        const input = document.getElementById('lunou-bot-input');
        if (micBtn) {
            micBtn.classList.remove('text-rose-500', 'bg-rose-50', 'animate-pulse');
            micBtn.classList.add('text-slate-400');
        }
        if (input) {
            input.placeholder = "Tanya LUNOU...";
        }
    }

    function playMascotClickSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            
            const ctx = new AudioContext();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.type = 'sine';
            
            // Frequency slides up: 450Hz to 850Hz in 0.12 seconds
            osc.frequency.setValueAtTime(450, ctx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(850, ctx.currentTime + 0.1);
            
            // Gain envelope: fast decay
            gain.gain.setValueAtTime(0.12, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
            
            osc.start();
            osc.stop(ctx.currentTime + 0.11);
        } catch (e) {
            console.error("Web Audio click sound failed:", e);
        }
    }

    function toggleLunouChatbot() {
        if (window.isDraggingBot) {
            return;
        }
        
        playMascotClickSound();

        const win = document.getElementById('lunou-chatbot-window');
        const mascot = document.getElementById('lunou-mascot-img');
        
        isBotWindowOpen = !isBotWindowOpen;

        if (isBotWindowOpen) {
            win.classList.remove('hidden');
            setTimeout(() => {
                win.classList.remove('translate-y-10', 'opacity-0');
                const container = document.getElementById('lunou-bot-messages');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }, 100);
            mascot.src = "{{ asset('icon/5.png') }}"; // Happy face when opened
        } else {
            win.classList.add('translate-y-10', 'opacity-0');
            setTimeout(() => {
                win.classList.add('hidden');
            }, 300);
            mascot.src = "{{ asset('icon/11.png') }}"; // Idle face when closed
        }
    }

    function sendLunouQuery(e) {
        e.preventDefault();
        
        const input = document.getElementById('lunou-bot-input');
        const queryText = input.value.trim();
        if (!queryText) return;

        // Clear input
        input.value = '';

        // Append user bubble
        appendBotBubble(queryText, true);

        // Update mascot state to thinking
        setMascotState('thinking');

        // Send AJAX Post Request
        fetch('{{ route("chatbot.query") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: queryText })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal mengambil respon");
            return res.json();
        })
        .then(data => {
            appendBotBubble(data.reply, false);
            setMascotState(data.emotion || 'happy');
        })
        .catch(err => {
            console.error(err);
            appendBotBubble("Ups! LUNOU kehilangan sinyal sesaat. Coba tanya lagi, ya!", false);
            setMascotState('idle');
        });
    }

    function setMascotState(emotion) {
        const mascot = document.getElementById('lunou-mascot-img');
        const headerMascot = document.getElementById('lunou-bot-header-avatar');
        const statusSpan = document.getElementById('lunou-bot-status');
        
        let iconName = '11.png'; // idle default
        let statusText = 'Sedia Membantu';
        
        if (emotion === 'happy') {
            iconName = '5.png';
            statusText = 'Tersenyum Bahagia';
            mascot.classList.remove('animate-heartbeat');
        } else if (emotion === 'excited') {
            iconName = '1.png';
            statusText = 'LUNOU Bersemangat!';
            mascot.classList.remove('animate-heartbeat');
        } else if (emotion === 'thinking') {
            iconName = '15.png';
            statusText = 'Sedang Berpikir...';
            mascot.classList.add('animate-heartbeat'); // Heartbeat pulse on thinking!
        } else if (emotion === 'sleeping') {
            iconName = '18.png';
            statusText = 'Mengantuk (Zzz...)';
            mascot.classList.remove('animate-heartbeat');
        } else {
            mascot.classList.remove('animate-heartbeat');
        }

        const newSrc = `/icon/${iconName}`;
        mascot.src = newSrc;
        if (headerMascot) headerMascot.src = newSrc;
        if (statusSpan) statusSpan.innerText = statusText;
    }

    function appendBotBubble(text, isUser = false) {
        const container = document.getElementById('lunou-bot-messages');
        if (!container) return;

        let bubbleHtml = "";

        if (isUser) {
            bubbleHtml = `
                <div class="flex items-start gap-2 justify-end">
                    <div class="max-w-[80%] bg-indigo-600 text-white rounded-2xl rounded-tr-none p-3 shadow-xs">
                        <p class="text-[11px] leading-relaxed font-semibold">${escapeHtml(text)}</p>
                    </div>
                </div>
            `;
        } else {
            // Support newline breaks and markdown links in chatbot answers
            const formattedText = parseMarkdownLinks(escapeHtml(text)).replace(/\n/g, '<br>');
            bubbleHtml = `
                <div class="flex items-start gap-2 justify-start">
                    <img src="/icon/11.png" alt="LUNOU" class="w-6 h-6 object-contain shrink-0 mt-0.5">
                    <div class="max-w-[80%] bg-white border border-slate-100 rounded-2xl rounded-tl-none p-3 shadow-xs">
                        <p class="text-[11px] text-slate-700 leading-relaxed font-semibold">${formattedText}</p>
                    </div>
                </div>
            `;
        }

        container.insertAdjacentHTML('beforeend', bubbleHtml);
        container.scrollTop = container.scrollHeight;
    }

    function parseMarkdownLinks(text) {
        return text.replace(/\[([^\]]+)\]\(([^)]+)\)/g, function(match, label, url) {
            return `<a href="${url}" class="text-indigo-600 hover:text-indigo-800 font-black underline transition-all pointer-events-auto">${label}</a>`;
        });
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Make Floating Mascot Chatbot Draggable
    function initMascotDrag() {
        const wrapper = document.getElementById('lunou-chatbot-wrapper');
        if (!wrapper) return;
        const triggerBtn = wrapper.querySelector('button[type="button"]');
        if (!triggerBtn) return;
        const mascotImg = document.getElementById('lunou-mascot-img');
        
        let isMouseDown = false;
        let startX = 0;
        let startY = 0;
        let originalLeft = 0;
        let originalTop = 0;
        let hasDragged = false;

        // Block native browser dragging ghost image
        triggerBtn.addEventListener('dragstart', function (e) {
            e.preventDefault();
        });
        if (mascotImg) {
            mascotImg.addEventListener('dragstart', function (e) {
                e.preventDefault();
            });
        }
        
        triggerBtn.addEventListener('mousedown', function (e) {
            isMouseDown = true;
            startX = e.clientX;
            startY = e.clientY;
            
            const rect = wrapper.getBoundingClientRect();
            originalLeft = rect.left;
            originalTop = rect.top;
            
            hasDragged = false;
            document.body.style.userSelect = 'none';
        });
        
        triggerBtn.addEventListener('touchstart', function (e) {
            isMouseDown = true;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            
            const rect = wrapper.getBoundingClientRect();
            originalLeft = rect.left;
            originalTop = rect.top;
            
            hasDragged = false;
            document.body.style.userSelect = 'none';
        }, { passive: true });
        
        document.addEventListener('mousemove', function (e) {
            if (!isMouseDown) return;
            
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            if (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5) {
                hasDragged = true;
                window.isDraggingBot = true;
            }
            
            if (hasDragged) {
                wrapper.classList.remove('bottom-24', 'md:bottom-6', 'right-6');
                
                let newLeft = originalLeft + deltaX;
                let newTop = originalTop + deltaY;
                
                const wrapperWidth = wrapper.offsetWidth;
                const wrapperHeight = wrapper.offsetHeight;
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                
                if (newLeft < 10) newLeft = 10;
                if (newTop < 10) newTop = 10;
                if (newLeft + wrapperWidth > viewportWidth - 10) newLeft = viewportWidth - wrapperWidth - 10;
                if (newTop + wrapperHeight > viewportHeight - 10) newTop = viewportHeight - wrapperHeight - 10;
                
                wrapper.style.setProperty('left', newLeft + 'px', 'important');
                wrapper.style.setProperty('top', newTop + 'px', 'important');
                wrapper.style.setProperty('bottom', 'auto', 'important');
                wrapper.style.setProperty('right', 'auto', 'important');
            }
        });
        
        document.addEventListener('touchmove', function (e) {
            if (!isMouseDown) return;
            
            const touch = e.touches[0];
            const deltaX = touch.clientX - startX;
            const deltaY = touch.clientY - startY;
            
            if (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5) {
                hasDragged = true;
                window.isDraggingBot = true;
            }
            
            if (hasDragged) {
                if (e.cancelable) {
                    e.preventDefault();
                }
                
                wrapper.classList.remove('bottom-24', 'md:bottom-6', 'right-6');
                
                let newLeft = originalLeft + deltaX;
                let newTop = originalTop + deltaY;
                
                const wrapperWidth = wrapper.offsetWidth;
                const wrapperHeight = wrapper.offsetHeight;
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                
                if (newLeft < 10) newLeft = 10;
                if (newTop < 10) newTop = 10;
                if (newLeft + wrapperWidth > viewportWidth - 10) newLeft = viewportWidth - wrapperWidth - 10;
                if (newTop + wrapperHeight > viewportHeight - 10) newTop = viewportHeight - wrapperHeight - 10;
                
                wrapper.style.setProperty('left', newLeft + 'px', 'important');
                wrapper.style.setProperty('top', newTop + 'px', 'important');
                wrapper.style.setProperty('bottom', 'auto', 'important');
                wrapper.style.setProperty('right', 'auto', 'important');
            }
        }, { passive: false });
        
        document.addEventListener('mouseup', function () {
            if (isMouseDown) {
                isMouseDown = false;
                document.body.style.userSelect = '';
                setTimeout(() => {
                    window.isDraggingBot = false;
                }, 100);
            }
        });
        
        document.addEventListener('touchend', function () {
            if (isMouseDown) {
                isMouseDown = false;
                document.body.style.userSelect = '';
                setTimeout(() => {
                    window.isDraggingBot = false;
                }, 100);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMascotDrag);
    } else {
        initMascotDrag();
    }
</script>
