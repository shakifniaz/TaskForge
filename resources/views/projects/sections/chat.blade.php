<x-app-layout>
    <x-projects.layout :project="$project" active="chat">
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Chat</h1>
                <p class="text-sm text-gray-500">Project group chat</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div id="chatBox" class="h-[60vh] overflow-y-auto p-4 space-y-3 bg-gray-50">
                    @php $me = auth()->id(); $lastId = 0; @endphp

                    @foreach($messages as $m)
                        @php
                            $isMe = $m->user_id === $me;
                            $lastId = $m->id;
                            $displayName = $m->user->username ? '@'.$m->user->username : $m->user->name;
                            $attachmentUrl = $m->attachment_path ? asset('storage/' . $m->attachment_path) : null;
                            $isImage = $m->attachment_mime && str_starts_with($m->attachment_mime, 'image/');
                        @endphp

                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%]">
                                <div class="text-xs text-gray-500 mb-1 {{ $isMe ? 'text-right' : 'text-left' }}">
                                    {{ $displayName }} · {{ $m->created_at->format('H:i') }}
                                </div>

                                <div class="{{ $isMe ? 'bg-gray-900 text-white' : 'bg-white text-gray-900' }} border border-gray-200 rounded-2xl px-4 py-2 shadow-sm space-y-2">
                                    @if(trim($m->body) !== '')
                                        <div class="whitespace-pre-wrap">{{ $m->body }}</div>
                                    @endif

                                    @if($attachmentUrl)
                                        @if($isImage)
                                            <a href="{{ $attachmentUrl }}" target="_blank" class="block">
                                                <img src="{{ $attachmentUrl }}" alt="image"
                                                     class="rounded-xl max-h-64 border border-gray-200">
                                            </a>
                                        @else
                                            <a href="{{ $attachmentUrl }}" target="_blank"
                                               class="underline text-sm {{ $isMe ? 'text-white/90' : 'text-gray-700' }}">
                                                📎 {{ $m->attachment_name ?? 'Download file' }}
                                            </a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-3 border-t border-gray-200 bg-white">
                    <form id="chatForm" class="flex gap-2 items-center">
                        @csrf

                        {{-- Hidden file input --}}
                        <input id="chatFile" type="file" class="hidden" />

                        {{-- Attach button --}}
                        <button type="button"
                                id="attachBtn"
                                class="px-3 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition"
                                title="Attach file">
                            📎
                        </button>

                        <input
                            id="chatInput"
                            name="body"
                            type="text"
                            autocomplete="off"
                            class="flex-1 rounded-xl border-gray-300 focus:border-gray-400 focus:ring-gray-300"
                            placeholder="Type a message…"
                            maxlength="2000"
                        />

                        <button
                            type="submit"
                            class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                            Send
                        </button>
                    </form>

                    <div id="fileInfo" class="text-xs text-gray-500 mt-2 hidden"></div>
                    <p id="chatError" class="text-xs text-red-600 mt-2 hidden"></p>
                </div>
            </div>
        </div>

        <script>
            const chatBox = document.getElementById('chatBox');
            const chatForm = document.getElementById('chatForm');
            const chatInput = document.getElementById('chatInput');
            const chatError = document.getElementById('chatError');

            const attachBtn = document.getElementById('attachBtn');
            const chatFile = document.getElementById('chatFile');
            const fileInfo = document.getElementById('fileInfo');

            const meId = {{ auth()->id() }};
            let lastId = {{ $lastId ?? 0 }};
            let isSending = false;
            let isPolling = false;

            const renderedIds = new Set();

            function scrollToBottom() { chatBox.scrollTop = chatBox.scrollHeight; }
            scrollToBottom();

            function escapeHtml(str) {
                return str.replace(/[&<>"']/g, (m) => ({
                    "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
                }[m]));
            }

            function humanSize(bytes) {
                if (!bytes && bytes !== 0) return '';
                const units = ['B','KB','MB','GB'];
                let i = 0;
                let n = bytes;
                while (n >= 1024 && i < units.length - 1) { n /= 1024; i++; }
                return `${n.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
            }

            function renderMessage(m) {
                if (renderedIds.has(m.id)) return;
                renderedIds.add(m.id);

                const isMe = m.user.id === meId;
                const displayName = m.user.username ? `@${m.user.username}` : m.user.name;

                const wrap = document.createElement('div');
                wrap.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;

                const inner = document.createElement('div');
                inner.className = 'max-w-[75%]';

                const meta = document.createElement('div');
                meta.className = `text-xs text-gray-500 mb-1 ${isMe ? 'text-right' : 'text-left'}`;
                meta.textContent = `${displayName} · ${new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}`;

                const bubble = document.createElement('div');
                bubble.className = `${isMe ? 'bg-gray-900 text-white' : 'bg-white text-gray-900'} border border-gray-200 rounded-2xl px-4 py-2 shadow-sm space-y-2`;

                if (m.body && m.body.trim() !== '') {
                    const body = document.createElement('div');
                    body.className = 'whitespace-pre-wrap';
                    body.innerHTML = escapeHtml(m.body);
                    bubble.appendChild(body);
                }

                if (m.attachment) {
                    const isImage = (m.attachment.mime || '').startsWith('image/');
                    if (isImage) {
                        const a = document.createElement('a');
                        a.href = m.attachment.url;
                        a.target = '_blank';
                        a.className = 'block';

                        const img = document.createElement('img');
                        img.src = m.attachment.url;
                        img.alt = 'image';
                        img.className = 'rounded-xl max-h-64 border border-gray-200';

                        a.appendChild(img);
                        bubble.appendChild(a);
                    } else {
                        const a = document.createElement('a');
                        a.href = m.attachment.url;
                        a.target = '_blank';
                        a.className = `underline text-sm ${isMe ? 'text-white/90' : 'text-gray-700'}`;
                        a.textContent = `📎 ${m.attachment.name || 'Download file'}`;
                        bubble.appendChild(a);
                    }
                }

                inner.appendChild(meta);
                inner.appendChild(bubble);
                wrap.appendChild(inner);
                chatBox.appendChild(wrap);
            }

            async function poll() {
                if (isPolling) return;
                isPolling = true;

                try {
                    const url = `{{ route('projects.chat.feed', $project) }}?after_id=${lastId}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                    if (!res.ok) return;

                    const data = await res.json();
                    if (Array.isArray(data) && data.length) {
                        data.forEach(m => {
                            renderMessage(m);
                            lastId = Math.max(lastId, m.id);
                        });
                        scrollToBottom();
                    }
                } catch (e) {
                    // ignore
                } finally {
                    isPolling = false;
                }
            }

            setInterval(poll, 2000);

            attachBtn.addEventListener('click', () => chatFile.click());

            chatFile.addEventListener('change', () => {
                if (!chatFile.files || !chatFile.files[0]) {
                    fileInfo.classList.add('hidden');
                    fileInfo.textContent = '';
                    return;
                }
                const f = chatFile.files[0];
                fileInfo.textContent = `Attached: ${f.name} (${humanSize(f.size)})`;
                fileInfo.classList.remove('hidden');
            });

            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                if (isSending) return;

                const body = chatInput.value.trim();
                const file = chatFile.files && chatFile.files[0] ? chatFile.files[0] : null;

                if (!body && !file) return;

                isSending = true;
                chatError.classList.add('hidden');
                chatInput.disabled = true;
                attachBtn.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('input[name="_token"]').value);
                    if (body) formData.append('body', body);
                    if (file) formData.append('file', file);

                    const res = await fetch(`{{ route('projects.chat.store', $project) }}`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: formData
                    });

                    if (!res.ok) {
                        chatError.textContent = 'Failed to send message/file.';
                        chatError.classList.remove('hidden');
                        return;
                    }

                    chatInput.value = '';
                    chatFile.value = '';
                    fileInfo.textContent = '';
                    fileInfo.classList.add('hidden');

                    await poll();
                } finally {
                    chatInput.disabled = false;
                    attachBtn.disabled = false;
                    chatInput.focus();
                    isSending = false;
                }
            });
        </script>
    </x-projects.layout>
</x-app-layout>
