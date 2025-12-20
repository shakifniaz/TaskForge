<x-app-layout>
    <x-projects.layout :project="$project" active="chat">

        {{-- ===== PAGE (NO SCROLL) ===== --}}
        <div class="h-[calc(100vh-8rem)] flex flex-col">

            {{-- Header --}}
            <div class="pb-3">
                <h1 class="text-2xl font-bold tf-h">Project Chat</h1>
            </div>

            {{-- ===== CHAT CARD ===== --}}
            <div class="flex-1 flex flex-col rounded-3xl border border-black/10 bg-white shadow-sm tf-card overflow-hidden">

                {{-- ===== MESSAGES (SCROLLABLE) ===== --}}
                <div id="chatBox"
                     class="flex-1 overflow-y-auto px-4 py-4 sm:px-6 space-y-4 tf-chat-bg">

                    @php
                        $me = auth()->id();
                        $lastId = 0;
                    @endphp

                    @foreach($messages as $m)
                        @php
                            $lastId = $m->id;
                            $isMe = $m->user_id === $me;
                            $displayName = $m->user->username ? '@'.$m->user->username : $m->user->name;
                            $attachmentUrl = $m->attachment_path ? asset('storage/'.$m->attachment_path) : null;
                            $isImage = $m->attachment_mime && str_starts_with($m->attachment_mime, 'image/');
                        @endphp

                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[70%]">

                                <div class="text-xs mb-1 tf-meta {{ $isMe ? 'text-right' : 'text-left' }}">
                                    {{ $displayName }} · {{ $m->created_at->format('H:i') }}
                                </div>

                                <div class="tf-bubble {{ $isMe ? 'tf-bubble-me' : 'tf-bubble-other' }}">
                                    @if($m->body)
                                        <div class="whitespace-pre-wrap">{{ $m->body }}</div>
                                    @endif

                                    @if($attachmentUrl)
                                        <div class="mt-2">
                                            @if($isImage)
                                                <a href="{{ $attachmentUrl }}" target="_blank">
                                                    <img src="{{ $attachmentUrl }}"
                                                         class="rounded-xl max-h-64 border hover:scale-[1.02] transition"
                                                         alt="attachment">
                                                </a>
                                            @else
                                                <a href="{{ $attachmentUrl }}"
                                                   target="_blank"
                                                   class="text-sm underline tf-link">
                                                    📎 {{ $m->attachment_name }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ===== INPUT BAR ===== --}}
                <div class="border-t border-black/10 p-3 sm:p-4 bg-white tf-inputbar">
                    <form id="chatForm" class="flex items-center gap-2">
                        @csrf

                        <input id="chatFile" type="file" class="hidden">

                        <button type="button"
                                id="attachBtn"
                                title="Attach file"
                                class="h-11 w-11 rounded-xl border border-black/10 bg-white
                                       hover:-translate-y-0.5 hover:shadow transition tf-btn-icon">
                            📎
                        </button>

                        <input id="chatInput"
                               name="body"
                               type="text"
                               placeholder="Type a message…"
                               autocomplete="off"
                               maxlength="2000"
                               class="flex-1 h-11 rounded-xl border border-black/10 px-4 tf-input">

                        <button type="submit"
                                class="h-11 px-5 rounded-xl font-semibold text-white tf-send">
                            Send
                        </button>
                    </form>

                    {{-- Attachment indicator --}}
                    <div id="fileInfo" class="text-xs mt-2 tf-sub hidden"></div>
                    <p id="chatError" class="hidden text-xs text-red-600 mt-2"></p>
                </div>
            </div>
        </div>

        {{-- ================= STYLES ================= --}}
        <style>
            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-meta { color:rgba(15,23,42,.55); }

            .tf-chat-bg { background:#f5f6f8; }

            .tf-bubble {
                padding:.65rem 1rem;
                border-radius:1rem;
                border:1px solid rgba(0,0,0,.08);
                box-shadow:0 6px 18px rgba(0,0,0,.08);
                line-height:1.45;
                word-break:break-word;
                transition:transform .15s ease;
            }

            .tf-bubble:hover { transform:translateY(-1px); }

            .tf-bubble-me {
                background:#0f172a;
                color:#fff;
                border-top-right-radius:.25rem;
            }

            .tf-bubble-other {
                background:#ffffff;
                color:#0f172a;
                border-top-left-radius:.25rem;
            }

            .tf-send { background:#57A773; }
            .tf-send:hover { background:#4d9a69; }

            /* DARK MODE */
            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub { color:rgba(233,238,245,.7); }
            html.dark .tf-meta { color:rgba(233,238,245,.45); }
            html.dark .tf-chat-bg { background:#0f141a; }
            html.dark .tf-card { background:#121a22; border-color:rgba(255,255,255,.08); }

            html.dark .tf-bubble-other {
                background:#1a2430;
                color:#e9eef5;
                border-color:rgba(255,255,255,.08);
            }

            html.dark .tf-bubble-me { background:#157145; }

            html.dark .tf-inputbar {
                background:#121a22;
                border-color:rgba(255,255,255,.08);
            }

            html.dark .tf-input {
                background:#0f141a;
                color:#e9eef5;
                border-color:rgba(255,255,255,.12);
            }

            html.dark .tf-btn-icon {
                background:#0f141a;
                border-color:rgba(255,255,255,.12);
                color:#e9eef5;
            }
        </style>

        {{-- ================= SCRIPT ================= --}}
        <script>
            const chatBox   = document.getElementById('chatBox');
            const chatForm  = document.getElementById('chatForm');
            const chatInput = document.getElementById('chatInput');
            const chatFile  = document.getElementById('chatFile');
            const attachBtn = document.getElementById('attachBtn');
            const fileInfo  = document.getElementById('fileInfo');
            const chatError = document.getElementById('chatError');

            const meId = {{ auth()->id() }};
            let lastId = {{ $lastId ?? 0 }};
            let sending = false;
            const renderedIds = new Set();

            function scrollBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
            scrollBottom();

            function renderMessage(m) {
                if (renderedIds.has(m.id)) return;
                renderedIds.add(m.id);

                const isMe = m.user.id === meId;

                const wrap = document.createElement('div');
                wrap.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;

                const inner = document.createElement('div');
                inner.className = 'max-w-[70%]';

                const meta = document.createElement('div');
                meta.className = `text-xs mb-1 tf-meta ${isMe ? 'text-right' : 'text-left'}`;
                meta.textContent =
                    `${m.user.username ? '@'+m.user.username : m.user.name} · ` +
                    new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});

                const bubble = document.createElement('div');
                bubble.className = `tf-bubble ${isMe ? 'tf-bubble-me' : 'tf-bubble-other'}`;

                if (m.body) {
                    const body = document.createElement('div');
                    body.className = 'whitespace-pre-wrap';
                    body.textContent = m.body;
                    bubble.appendChild(body);
                }

                if (m.attachment) {
                    const att = document.createElement('div');
                    att.className = 'mt-2';

                    if ((m.attachment.mime || '').startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = m.attachment.url;
                        img.className = 'rounded-xl max-h-64 border';
                        att.appendChild(img);
                    } else {
                        const a = document.createElement('a');
                        a.href = m.attachment.url;
                        a.target = '_blank';
                        a.className = 'text-sm underline tf-link';
                        a.textContent = `📎 ${m.attachment.name}`;
                        att.appendChild(a);
                    }
                    bubble.appendChild(att);
                }

                inner.appendChild(meta);
                inner.appendChild(bubble);
                wrap.appendChild(inner);
                chatBox.appendChild(wrap);
                scrollBottom();
            }

            async function poll() {
                try {
                    const res = await fetch(`{{ route('projects.chat.feed',$project) }}?after_id=${lastId}`);
                    if (!res.ok) return;
                    const data = await res.json();
                    data.forEach(m => {
                        renderMessage(m);
                        lastId = Math.max(lastId, m.id);
                    });
                } catch {}
            }
            setInterval(poll, 2000);

            attachBtn.onclick = () => chatFile.click();

            chatFile.onchange = () => {
                if (!chatFile.files[0]) {
                    fileInfo.classList.add('hidden');
                    return;
                }
                fileInfo.textContent = `Attached: ${chatFile.files[0].name}`;
                fileInfo.classList.remove('hidden');
            };

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (sending) return;

                const body = chatInput.value.trim();
                const file = chatFile.files[0] || null;
                if (!body && !file) return;

                sending = true;
                chatInput.disabled = true;
                attachBtn.disabled = true;

                try {
                    const fd = new FormData();
                    fd.append('_token', document.querySelector('input[name="_token"]').value);
                    if (body) fd.append('body', body);
                    if (file) fd.append('file', file);

                    const res = await fetch(`{{ route('projects.chat.store',$project) }}`, {
                        method:'POST',
                        headers:{ 'Accept':'application/json' },
                        body:fd
                    });

                    if (!res.ok) throw new Error();

                    chatInput.value = '';
                    chatFile.value = '';
                    fileInfo.classList.add('hidden');
                    await poll();
                } catch {
                    chatError.textContent = 'Failed to send message';
                    chatError.classList.remove('hidden');
                } finally {
                    sending = false;
                    chatInput.disabled = false;
                    attachBtn.disabled = false;
                    chatInput.focus();
                }
            });
        </script>

    </x-projects.layout>
</x-app-layout>
