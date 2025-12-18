<x-app-layout>
    <x-projects.layout :project="$project" active="files">
        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Files</h1>
                    <p class="text-sm text-gray-500">Uploads and repository browser</p>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <button id="tabUploads"
                            class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-gray-900 text-white">
                        Uploads
                    </button>

                    <button id="tabRepo"
                            class="px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50">
                        Repository
                    </button>

                    <div class="ml-auto text-sm text-gray-500" id="repoStatus"></div>
                </div>
            </div>

            {{-- Uploads panel (default visible) --}}
            <div id="panelUploads" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Uploads</h2>
                        <p class="text-sm text-gray-500">Upload files and optionally attach to a task</p>
                    </div>
                </div>

                @if(session('status') === 'file-uploaded')
                    <div class="mt-3 text-sm text-green-700">File uploaded.</div>
                @endif

                @if(session('status') === 'file-deleted')
                    <div class="mt-3 text-sm text-green-700">File deleted.</div>
                @endif

                @if($errors->any())
                    <div class="mt-3 text-sm text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Upload form --}}
                <form method="POST" action="{{ route('projects.files.upload', $project) }}"
                      enctype="multipart/form-data"
                      class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">File</label>
                        <input type="file" name="file" required class="w-full rounded-xl border border-gray-200 bg-white p-2">
                        <div class="text-xs text-gray-400 mt-1">Max 10MB</div>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Attach to task (optional)</label>
                        <select name="task_id" class="w-full rounded-xl border-gray-300">
                            <option value="">No task</option>
                            @foreach($tasks as $t)
                                <option value="{{ $t->id }}">{{ $t->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <button type="submit"
                                class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                            Upload
                        </button>
                    </div>
                </form>

                {{-- Upload list --}}
                <div class="mt-6 space-y-3">
                    @forelse($uploads as $f)
                        <div class="p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $f->original_name }}</div>

                                    <div class="mt-1 text-sm text-gray-600">
                                        Uploaded by
                                        <span class="font-medium">
                                            {{ $f->uploader?->username ? '@'.$f->uploader->username : ($f->uploader?->name ?? 'Unknown') }}
                                        </span>
                                        · {{ $f->created_at->format('Y-m-d H:i') }}
                                        · {{ number_format(($f->size ?? 0) / 1024, 1) }} KB
                                    </div>

                                    @if($f->task)
                                        <div class="mt-1 text-sm text-gray-600">
                                            Attached to task: <span class="font-medium">{{ $f->task->title }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('projects.files.download', [$project, $f]) }}"
                                       class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-gray-100 text-sm">
                                        Download
                                    </a>

                                    <form method="POST" action="{{ route('projects.files.destroy', [$project, $f]) }}"
                                          onsubmit="return confirm('Delete this file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="px-3 py-2 rounded-lg border border-gray-200 bg-white hover:bg-red-50 text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">No uploads yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Repository panel (hidden by default) --}}
            <div id="panelRepo" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hidden">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Repository</h2>
                        <p class="text-sm text-gray-500">Browse GitHub files (read-only)</p>
                    </div>

                    <div class="flex items-center gap-2" id="repoTopControls">
                        <input id="repoPath"
                               type="text"
                               class="rounded-xl border-gray-300 text-sm w-64"
                               placeholder="Path (e.g. / or src)"
                               value="">
                        <button id="repoGo"
                                class="px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                            Go
                        </button>
                    </div>
                </div>

                @if(!$githubConnected)
                    <div class="mt-4 p-4 rounded-xl border border-yellow-200 bg-yellow-50 text-sm text-yellow-800">
                        This project is not connected to GitHub yet. Go to <b>Activity</b> and connect a repo first.
                    </div>
                @endif

                <div class="mt-5">
                    {{-- Repo browser view (list) --}}
                    <div id="repoBrowserWrap">
                        <div class="text-sm text-gray-600">
                            Current path: <span class="font-medium" id="repoCrumb">/</span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2" id="repoList">
                            <div class="text-sm text-gray-500">Loading…</div>
                        </div>
                    </div>

                    {{-- Preview view (hidden until file click) --}}
                    <div id="repoPreviewWrap" class="hidden">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <button id="repoBackBtn"
                                        type="button"
                                        class="px-3 py-2 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-sm font-semibold">
                                    ← Back
                                </button>

                                <div class="font-semibold text-gray-900" id="repoPreviewTitle">Preview</div>
                            </div>

                            <a id="repoDownloadLink"
                               href="#"
                               target="_blank"
                               class="px-3 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800 transition hidden">
                                Download
                            </a>
                        </div>

                        <pre id="repoPreview"
                             class="mt-3 text-xs rounded-xl border border-gray-200 bg-gray-50 p-4 overflow-auto max-h-[60vh] whitespace-pre-wrap"></pre>

                        <p id="repoPreviewNote" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const tabUploads = document.getElementById('tabUploads');
            const tabRepo = document.getElementById('tabRepo');
            const panelUploads = document.getElementById('panelUploads');
            const panelRepo = document.getElementById('panelRepo');
            const repoStatus = document.getElementById('repoStatus');

            function setActiveTab(which) {
                if (which === 'uploads') {
                    panelUploads.classList.remove('hidden');
                    panelRepo.classList.add('hidden');

                    tabUploads.className = "px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-gray-900 text-white";
                    tabRepo.className = "px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50";
                    repoStatus.textContent = '';
                } else {
                    panelRepo.classList.remove('hidden');
                    panelUploads.classList.add('hidden');

                    tabRepo.className = "px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-gray-900 text-white";
                    tabUploads.className = "px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-white hover:bg-gray-50";
                }
            }

            tabUploads.addEventListener('click', () => setActiveTab('uploads'));
            tabRepo.addEventListener('click', () => { setActiveTab('repo'); loadRepo(''); });

            const repoList = document.getElementById('repoList');
            const repoCrumb = document.getElementById('repoCrumb');
            const repoPath = document.getElementById('repoPath');
            const repoGo = document.getElementById('repoGo');

            const repoBrowserWrap = document.getElementById('repoBrowserWrap');
            const repoPreviewWrap = document.getElementById('repoPreviewWrap');
            const repoPreview = document.getElementById('repoPreview');
            const repoPreviewTitle = document.getElementById('repoPreviewTitle');
            const repoDownloadLink = document.getElementById('repoDownloadLink');
            const repoPreviewNote = document.getElementById('repoPreviewNote');
            const repoBackBtn = document.getElementById('repoBackBtn');

            let currentPath = '';

            function esc(s) {
                return (s || '').replace(/[&<>"']/g, (m) => ({
                    "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
                }[m]));
            }

            function showRepoListView() {
                repoPreviewWrap.classList.add('hidden');
                repoBrowserWrap.classList.remove('hidden');

                repoDownloadLink.classList.add('hidden');
                repoPreviewNote.classList.add('hidden');
                repoPreview.textContent = '';
            }

            function showRepoPreviewView() {
                repoBrowserWrap.classList.add('hidden');
                repoPreviewWrap.classList.remove('hidden');
            }

            repoBackBtn.addEventListener('click', () => {
                showRepoListView();
            });

            async function loadRepo(path) {
                currentPath = (path || '').replace(/^\/+|\/+$/g, '');
                repoCrumb.textContent = '/' + (currentPath ? currentPath : '');
                repoPath.value = currentPath;

                showRepoListView();

                repoStatus.textContent = 'Loading repository…';
                repoList.innerHTML = `<div class="text-sm text-gray-500">Loading…</div>`;

                try {
                    const url = `{{ route('projects.files.repo', $project) }}?path=${encodeURIComponent(currentPath)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    if (!data.connected) {
                        repoStatus.textContent = 'Not connected';
                        repoList.innerHTML = `<div class="text-sm text-gray-500">${esc(data.message || 'Connect GitHub in Activity tab.')}</div>`;
                        return;
                    }

                    if (data.message) {
                        repoStatus.textContent = data.message;
                        repoList.innerHTML = `<div class="text-sm text-gray-500">${esc(data.message)}</div>`;
                        return;
                    }

                    repoStatus.textContent = '';

                    const items = data.items || [];

                    const parts = currentPath ? currentPath.split('/') : [];
                    const upPath = parts.length > 1 ? parts.slice(0, -1).join('/') : '';
                    const showUp = (currentPath !== '');

                    let html = '';

                    if (showUp) {
                        html += `
                            <button class="text-left p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50"
                                    data-type="dir" data-path="${esc(upPath)}">
                                <div class="font-semibold text-gray-900">⬅ ..</div>
                                <div class="text-xs text-gray-500">Go up</div>
                            </button>
                        `;
                    }

                    if (!items.length) {
                        html += `<div class="text-sm text-gray-500">No files found.</div>`;
                        repoList.innerHTML = html;
                        return;
                    }

                    items.forEach(i => {
                        const icon = i.type === 'dir' ? '📁' : '📄';
                        const meta = i.type === 'dir' ? 'Folder' : 'File';
                        html += `
                            <button class="text-left p-3 rounded-xl border border-gray-200 bg-white hover:bg-gray-50"
                                    data-type="${esc(i.type)}"
                                    data-path="${esc(i.path)}"
                                    data-name="${esc(i.name)}"
                                    data-download="${esc(i.download_url || '')}">
                                <div class="font-semibold text-gray-900">${icon} ${esc(i.name)}</div>
                                <div class="text-xs text-gray-500">${meta}</div>
                            </button>
                        `;
                    });

                    repoList.innerHTML = html;

                    repoList.querySelectorAll('button[data-path]').forEach(btn => {
                        btn.addEventListener('click', async () => {
                            const type = btn.dataset.type;
                            const p = btn.dataset.path || '';
                            if (type === 'dir') {
                                loadRepo(p);
                            } else {
                                await previewFile(p, btn.dataset.name || 'Preview', btn.dataset.download || '');
                            }
                        });
                    });

                } catch (e) {
                    repoStatus.textContent = 'Error';
                    repoList.innerHTML = `<div class="text-sm text-gray-500">Failed to load repository.</div>`;
                }
            }

            async function previewFile(path, name, downloadUrl) {
                repoStatus.textContent = 'Loading file…';

                showRepoPreviewView();

                repoPreviewTitle.textContent = `Preview: ${name || 'File'}`;
                repoPreview.textContent = '';
                repoPreviewNote.classList.add('hidden');
                repoDownloadLink.classList.add('hidden');

                try {
                    const url = `{{ route('projects.files.repo.view', $project) }}?path=${encodeURIComponent(path)}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    repoStatus.textContent = '';

                    const dl = `{{ route('projects.files.repo.download', $project) }}?path=${encodeURIComponent(path)}`;
                    repoDownloadLink.href = dl;
                    repoDownloadLink.classList.remove('hidden');

                    if (data.text === null) {
                        repoPreview.textContent = '';
                        repoPreviewNote.textContent = data.note || 'Preview not available. Use download.';
                        repoPreviewNote.classList.remove('hidden');
                        return;
                    }

                    repoPreview.textContent = data.text || '';
                } catch (e) {
                    repoStatus.textContent = 'Error';
                    repoPreviewNote.textContent = 'Failed to load file preview.';
                    repoPreviewNote.classList.remove('hidden');
                }
            }

            repoGo.addEventListener('click', () => loadRepo(repoPath.value || ''));

            setActiveTab('uploads');
        </script>
    </x-projects.layout>
</x-app-layout>
