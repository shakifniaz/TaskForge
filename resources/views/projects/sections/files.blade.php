<x-app-layout>
    <x-projects.layout :project="$project" active="files">

        <div class="space-y-6">

            {{-- ===== Header ===== --}}
            <div class="text-center">
                <h1 class="text-2xl font-bold tf-h">Files</h1>
                <p class="text-sm tf-sub">Uploads and repository browser</p>
            </div>

            {{-- ===== Tabs ===== --}}
            <div class="flex justify-center">
                <div class="flex gap-3 rounded-2xl border border-black/10 bg-white p-2 tf-card">
                    <button id="tabUploads" class="tf-tab tf-tab-active" type="button">
                        Uploads
                    </button>
                    <button id="tabRepo" class="tf-tab" type="button">
                        Repository
                    </button>
                </div>
            </div>

            {{-- ===== Uploads Panel ===== --}}
            <div id="panelUploads" class="rounded-3xl border border-black/10 bg-white shadow-sm p-6 tf-card tf-hovercard">

                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold tf-h">Uploads</h2>
                    <p class="text-sm tf-sub">Upload files and attach them to tasks</p>
                </div>

                {{-- Alerts --}}
                @if(session('status') === 'file-uploaded')
                    <div class="mb-3 text-sm text-green-600 text-center">File uploaded.</div>
                @endif
                @if(session('status') === 'file-deleted')
                    <div class="mb-3 text-sm text-green-600 text-center">File deleted.</div>
                @endif
                @if($errors->any())
                    <div class="mb-3 text-sm text-red-600 text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Upload Form --}}
                <form method="POST"
                      action="{{ route('projects.files.upload', $project) }}"
                      enctype="multipart/form-data"
                      class="flex flex-col items-center gap-4">
                    @csrf

                    <div class="flex flex-col md:flex-row gap-4 items-end justify-center w-full">
                        <div class="w-full md:w-80">
                            <label class="block text-xs tf-kicker mb-1">File</label>
                            <input type="file" name="file" required class="w-full tf-input">
                        </div>

                        <div class="w-full md:w-80">
                            <label class="block text-xs tf-kicker mb-1">Attach to task</label>
                            <select name="task_id" class="w-full tf-input">
                                <option value="">No task</option>
                                @foreach($tasks as $t)
                                    <option value="{{ $t->id }}">{{ $t->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-4 px-6 py-3 rounded-2xl text-white font-semibold tf-primary hover:-translate-y-0.5 hover:shadow-lg transition">
                        Upload File
                    </button>
                </form>

                {{-- Upload List --}}
                <div class="mt-8 space-y-3">
                    @forelse($uploads as $f)
                        <div class="rounded-2xl border border-black/10 bg-white p-4 tf-item tf-hoveritem tf-file-card">
                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">
                                    <div class="font-semibold tf-h truncate">{{ $f->original_name }}</div>
                                    <div class="mt-1 text-sm tf-sub">
                                        Uploaded by
                                        <span class="font-semibold">
                                            {{ $f->uploader?->username ? '@'.$f->uploader->username : ($f->uploader?->name ?? 'Unknown') }}
                                        </span>
                                        · {{ $f->created_at->format('Y-m-d H:i') }}
                                    </div>

                                    @if($f->task)
                                        <div class="mt-1 text-sm tf-sub">
                                            Attached to: <span class="font-semibold">{{ $f->task->title }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex gap-2 shrink-0">
                                    <a href="{{ route('projects.files.download', [$project, $f]) }}"
                                       class="tf-action-btn">
                                        Download
                                    </a>

                                    <form method="POST"
                                          action="{{ route('projects.files.destroy', [$project, $f]) }}"
                                          onsubmit="return confirm('Delete this file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tf-delete-btn">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    @empty
                        <p class="text-sm tf-sub text-center opacity-70">No uploads yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- ===== Repository Panel ===== --}}
            <div id="panelRepo" class="hidden rounded-3xl border border-black/10 bg-white shadow-sm p-6 tf-card tf-hovercard">

                <div class="text-center mb-6">
                    <h2 class="text-lg font-bold tf-h">Repository</h2>
                    <p class="text-sm tf-sub">Browse GitHub files (read-only)</p>
                </div>

                <div id="repoBrowserWrap">
                    <div class="text-sm tf-sub mb-3">
                        Current path: <span id="repoCrumb" class="font-semibold">/</span>
                    </div>

                    <div id="repoList" class="grid grid-cols-1 gap-2">
                        <div class="text-sm tf-sub">Loading…</div>
                    </div>
                </div>

                <div id="repoPreviewWrap" class="hidden">
                    <div class="flex items-center justify-between gap-3">
                        <button id="repoBackBtn" class="tf-action-btn">← Back</button>
                        <a id="repoDownloadLink" class="tf-primary px-4 py-2 rounded-xl text-white hidden">Download</a>
                    </div>

                    <pre id="repoPreview" class="mt-4 tf-code"></pre>
                    <p id="repoPreviewNote" class="mt-2 text-sm tf-sub hidden"></p>
                </div>
            </div>

        </div>

        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabUploads = document.getElementById('tabUploads');
            const tabRepo = document.getElementById('tabRepo');
            const panelUploads = document.getElementById('panelUploads');
            const panelRepo = document.getElementById('panelRepo');

            const repoList = document.getElementById('repoList');
            const repoCrumb = document.getElementById('repoCrumb');
            const repoBrowserWrap = document.getElementById('repoBrowserWrap');
            const repoPreviewWrap = document.getElementById('repoPreviewWrap');
            const repoPreview = document.getElementById('repoPreview');
            const repoPreviewNote = document.getElementById('repoPreviewNote');
            const repoBackBtn = document.getElementById('repoBackBtn');
            const repoDownloadLink = document.getElementById('repoDownloadLink');

            let currentPath = '';

            function esc(str) {
                return (str || '').replace(/[&<>"']/g, m => ({
                    "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"
                }[m]));
            }

            function activate(tab) {
                if (tab === 'uploads') {
                    panelUploads.classList.remove('hidden');
                    panelRepo.classList.add('hidden');
                    tabUploads.classList.add('tf-tab-active');
                    tabRepo.classList.remove('tf-tab-active');
                } else {
                    panelRepo.classList.remove('hidden');
                    panelUploads.classList.add('hidden');
                    tabRepo.classList.add('tf-tab-active');
                    tabUploads.classList.remove('tf-tab-active');
                    loadRepo('');
                }
            }

            tabUploads.addEventListener('click', () => activate('uploads'));
            tabRepo.addEventListener('click', () => activate('repo'));

            repoBackBtn.addEventListener('click', () => {
                repoPreviewWrap.classList.add('hidden');
                repoBrowserWrap.classList.remove('hidden');
                repoDownloadLink.classList.add('hidden');
            });

            async function loadRepo(path) {
                currentPath = path || '';
                repoCrumb.textContent = '/' + currentPath;

                repoBrowserWrap.classList.remove('hidden');
                repoPreviewWrap.classList.add('hidden');

                repoList.innerHTML = `<div class="text-sm tf-sub">Loading…</div>`;

                try {
                    const res = await fetch(
                        `{{ route('projects.files.repo', $project) }}?path=${encodeURIComponent(currentPath)}`
                    );
                    const data = await res.json();

                    if (!data.connected) {
                        repoList.innerHTML = `<div class="text-sm tf-sub">${esc(data.message)}</div>`;
                        return;
                    }

                    let items = data.items || [];

                    // folders first, then files
                    items.sort((a, b) => {
                        if (a.type === b.type) return a.name.localeCompare(b.name);
                        return a.type === 'dir' ? -1 : 1;
                    });

                    let html = '';

                    if (currentPath !== '') {
                        const parent = currentPath.split('/').slice(0, -1).join('/');
                        html += `
                            <button class="text-left p-3 rounded-xl border border-black/10 bg-white hover:bg-black/5"
                                    data-type="dir" data-path="${parent}">
                                <div class="font-semibold tf-h">⬅ Back</div>
                            </button>
                        `;
                    }

                    items.forEach(i => {
                        html += `
                            <button class="text-left p-3 rounded-xl border border-black/10 bg-white hover:bg-black/5"
                                    data-type="${i.type}"
                                    data-path="${esc(i.path)}"
                                    data-name="${esc(i.name)}"
                                    data-download="${esc(i.download_url || '')}">
                                <div class="font-semibold tf-h">
                                    ${i.type === 'dir' ? '📁' : '📄'} ${esc(i.name)}
                                </div>
                            </button>
                        `;
                    });

                    repoList.innerHTML = html;

                    repoList.querySelectorAll('button').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (btn.dataset.type === 'dir') {
                                loadRepo(btn.dataset.path);
                            } else {
                                previewFile(
                                    btn.dataset.path,
                                    btn.dataset.name,
                                    btn.dataset.download
                                );
                            }
                        });
                    });

                } catch {
                    repoList.innerHTML = `<div class="text-sm tf-sub">Failed to load repository.</div>`;
                }
            }

            async function previewFile(path, name) {
                repoBrowserWrap.classList.add('hidden');
                repoPreviewWrap.classList.remove('hidden');
                repoPreview.textContent = 'Loading…';
                repoPreviewNote.classList.add('hidden');
                repoDownloadLink.classList.add('hidden');

                try {
                    const res = await fetch(
                        `{{ route('projects.files.repo.view', $project) }}?path=${encodeURIComponent(path)}`,
                        { headers: { 'Accept': 'application/json' } }
                    );

                    const data = await res.json();

                    if (data.text === null) {
                        repoPreview.textContent = '';
                        repoPreviewNote.textContent = data.note || 'Preview not available.';
                        repoPreviewNote.classList.remove('hidden');
                    } else {
                        repoPreview.textContent = data.text;
                    }

                    repoDownloadLink.href =
                        `{{ route('projects.files.repo.download', $project) }}?path=${encodeURIComponent(path)}`;
                    repoDownloadLink.classList.remove('hidden');

                } catch (e) {
                    repoPreview.textContent = '';
                    repoPreviewNote.textContent = 'Failed to load file.';
                    repoPreviewNote.classList.remove('hidden');
                    repoDownloadLink.classList.add('hidden');
                }
            }

        });
        </script>




        {{-- ===== Styles ===== --}}
        <style>
            /* =====================
            LIGHT MODE BASE
            ====================== */

            .tf-h { color:#0f172a; }
            .tf-sub { color:rgba(15,23,42,.7); }
            .tf-kicker { color:rgba(15,23,42,.5); }

            .tf-primary { background:#57A773; }
            .tf-primary:hover { background:#4d9a69; }

            /* Inputs (file + select equal height) */
            .tf-input {
                height: 44px;
                padding: 0 0.75rem;
                border-radius: 0.75rem;
                border: 1px solid rgba(0,0,0,.12);
                background: #fff;
                font-size: 0.9rem;
                line-height: 44px;
            }

            /* Fix file input inner alignment */
            .tf-input[type="file"] {
                padding: 0.45rem 0.75rem;
                line-height: normal;
            }

            .tf-tab {
                padding:.6rem 1.25rem;
                border-radius:1rem;
                font-weight:600;
                transition:.2s;
            }

            .tf-tab-active {
                background:#57A773;
                color:#fff;
            }

            .tf-action-btn {
                padding:.5rem 1rem;
                border-radius:.75rem;
                border:1px solid rgba(0,0,0,.12);
                background:#fff;
                font-weight:600;
                transition:.15s;
            }

            .tf-action-btn:hover {
                background:rgba(0,0,0,.05);
            }

            .tf-delete-btn {
                padding:.5rem 1rem;
                border-radius:.75rem;
                border:1px solid rgba(0,0,0,.12);
                background:#fff;
                font-weight:600;
                transition:.15s;
            }

            .tf-delete-btn:hover {
                background:#dc2626;
                color:#fff;
            }

            .tf-code {
                background:#f8fafc;
                border-radius:1rem;
                padding:1rem;
                max-height:60vh;
                overflow:auto;
                font-size:.8rem;
            }

            /* =====================
            DARK MODE
            ====================== */

            html.dark .tf-h { color:#e9eef5; }
            html.dark .tf-sub,
            html.dark .tf-kicker { color:rgba(233,238,245,.7); }

            html.dark .tf-card {
                background:#121a22;
                border-color:rgba(255,255,255,.08);
            }

            /* File cards & repo items */
            html.dark .tf-file-card,
            html.dark #repoList button {
                background:#0f141a;
                border-color:rgba(255,255,255,.12);
                color:#e9eef5;
            }

            html.dark .tf-file-card .tf-h,
            html.dark .tf-file-card .tf-sub {
                color:#e9eef5;
            }

            /* Inputs in dark mode */
            html.dark .tf-input {
                background:#0f141a;
                color:#e9eef5;
                border-color:rgba(255,255,255,.18);
            }

            html.dark .tf-input::file-selector-button {
                background:#1a2430;
                color:#e9eef5;
                border:none;
                padding:0.4rem 0.75rem;
                border-radius:0.5rem;
                margin-right:0.75rem;
                cursor:pointer;
            }

            html.dark .tf-input::file-selector-button:hover {
                background:#233142;
            }

            html.dark select.tf-input option {
                background:#0f141a;
                color:#e9eef5;
            }

            /* Tabs */
            html.dark .tf-tab {
                color:#ffffff;
            }

            html.dark .tf-tab-active {
                background:#57A773;
                color:#ffffff;
            }

            /* Action buttons */
            html.dark .tf-action-btn {
                background:#0f141a;
                color:#ffffff;
                border-color:rgba(255,255,255,.15);
            }

            html.dark .tf-action-btn:hover {
                background:#1a2430;
            }

            /* Delete button */
            html.dark .tf-delete-btn {
                background:#1a2430;
                color:#ffffff;
                border-color:rgba(255,255,255,.15);
            }

            html.dark .tf-delete-btn:hover {
                background:#ef4444;
                color:#ffffff;
            }

            /* Code preview */
            html.dark .tf-code {
                background:#0f141a;
                color:#e9eef5;
            }
        </style>


    </x-projects.layout>
</x-app-layout>
