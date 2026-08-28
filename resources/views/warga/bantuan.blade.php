@extends('layouts.app')

@section('title', 'Bantuan & Asisten AI Nagari')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .ai-chat-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 620px;
    }

    .ai-chat-header {
        background: linear-gradient(135deg, #102c1e 0%, #1e3b2c 100%);
        color: white;
        padding: 20px 28px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ai-chat-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 14px 18px;
        border-radius: 18px;
        font-size: 0.92rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .chat-bubble-ai {
        background: white;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        border-bottom-left-radius: 4px;
        align-self: flex-start;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .chat-bubble-user {
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        border-bottom-right-radius: 4px;
        align-self: flex-end;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
    }

    .ai-chat-footer {
        padding: 16px 20px;
        background: white;
        border-top: 1px solid #e2e8f0;
    }

    .quick-pill {
        cursor: pointer;
        font-size: 0.8rem;
        padding: 6px 14px;
        border-radius: 20px;
        background: white;
        border: 1px solid #cbd5e1;
        color: #334155;
        transition: all 0.2s ease;
        display: inline-block;
        margin: 2px;
    }

    .quick-pill:hover {
        background: #e8f5e9;
        border-color: #198754;
        color: #198754;
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 950px;">
    <div class="row g-4">
        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="ai-chat-card">
                <div class="ai-chat-header">
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">Asisten AI Nagari Koto Malintang</h5>
                        <small class="text-white-50"><span class="badge bg-success me-1">Aktif</span> Terhubung dengan Hugging Face AI Engine</small>
                    </div>
                </div>

                <div class="ai-chat-body" id="chatBox">
                    <div class="chat-bubble chat-bubble-ai">
                        <strong>Halo {{ auth()->user()->name }}! 👋</strong><br>
                        Saya asisten cerdas pelayanan Nagari Koto Malintang. Ada yang bisa saya bantu terkait permohonan surat keterangan, persyaratan berkas, atau jadwal kantor nagari?
                    </div>
                </div>

                <div class="p-2 px-3 bg-light border-top d-flex flex-wrap gap-1 align-items-center">
                    <span class="small text-muted fw-bold me-1">Pertanyaan Cepat:</span>
                    <span class="quick-pill" onclick="sendQuick('Apa saja syarat membuat Surat Keterangan Usaha (SKU)?')">Syarat SKU</span>
                    <span class="quick-pill" onclick="sendQuick('Bagaimana cara mengajukan Surat Tidak Mampu (SKTM)?')">Syarat SKTM</span>
                    <span class="quick-pill" onclick="sendQuick('Kapan jam buka pelayanan kantor Wali Nagari?')">Jam Kantor</span>
                    <span class="quick-pill" onclick="sendQuick('Surat apa yang cocok untuk mendaftar nikah ke KUA?')">Syarat Nikah</span>
                </div>

                <div class="ai-chat-footer">
                    <form id="chatForm" onsubmit="handleSend(event)" class="d-flex gap-2">
                        <input type="text" id="userInput" class="form-control rounded-pill px-4" placeholder="Ketik pertanyaan Anda seputar layanan surat nagari..." required autocomplete="off" />
                        <button type="submit" id="btnSend" class="btn btn-success rounded-pill px-4 fw-bold">
                            <i class="bi bi-send-fill me-1"></i> Kirim
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Panduan 7 Surat -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="bi bi-card-checklist text-success me-2"></i>7 Layanan Surat Resmi Nagari
                </h6>
                <div class="list-group list-group-flush small">
                    @foreach ($daftarSurat as $key => $surat)
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark">{{ $surat['nama'] }}</strong>
                                <span class="badge bg-success bg-opacity-10 text-success">{{ $surat['singkatan'] }}</span>
                            </div>
                            <p class="text-muted mb-2" style="font-size: 0.8rem;">{{ Str::limit($surat['deskripsi'], 65) }}</p>
                            <a href="{{ route('warga.surat.create', ['jenis' => $key]) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-0" style="font-size: 0.75rem;">
                                Ajukan Surat Ini <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function appendMessage(sender, text) {
        const chatBox = document.getElementById('chatBox');
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble ' + (sender === 'user' ? 'chat-bubble-user' : 'chat-bubble-ai');
        bubble.innerText = text;
        chatBox.appendChild(bubble);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function sendQuick(text) {
        document.getElementById('userInput').value = text;
        handleSend(new Event('submit'));
    }

    async function handleSend(e) {
        e.preventDefault();
        const input = document.getElementById('userInput');
        const btn = document.getElementById('btnSend');
        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';
        input.disabled = true;
        btn.disabled = true;

        // Tampilkan loading bubble
        const chatBox = document.getElementById('chatBox');
        const loadingBubble = document.createElement('div');
        loadingBubble.className = 'chat-bubble chat-bubble-ai text-muted';
        loadingBubble.id = 'loadingBubble';
        loadingBubble.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Asisten AI sedang mengetik...';
        chatBox.appendChild(loadingBubble);
        chatBox.scrollTop = chatBox.scrollHeight;

        try {
            const response = await fetch("{{ route('warga.bantuan.chat') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            document.getElementById('loadingBubble')?.remove();

            if (data.success && data.reply) {
                appendMessage('ai', data.reply);
            } else {
                appendMessage('ai', 'Maaf, terjadi kendala saat memproses jawaban. Silakan coba sesaat lagi.');
            }
        } catch (err) {
            document.getElementById('loadingBubble')?.remove();
            appendMessage('ai', 'Koneksi terputus. Silakan periksa jaringan Anda.');
        } finally {
            input.disabled = false;
            btn.disabled = false;
            input.focus();
        }
    }
</script>
@endpush
@endsection
