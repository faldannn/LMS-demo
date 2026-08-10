@php
    $filePath = $filePath ?? null;
    $fileName = $fileName ?? null;
    $fileSize = $fileSize ?? null;
    $mimeType = $mimeType ?? null;
    $url = $url ?? null;
    $type = $type ?? null;
    $fileUrl = $filePath ? asset('storage/' . $filePath) : null;

    // Detect YouTube URL
    $youtubeId = null;
    if ($type === 'youtube' || ($url && (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')))) {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
            $youtubeId = $matches[1];
        }
    }

    // Extension & Category Detection
    $ext = strtolower(pathinfo($fileName ?? $filePath ?? '', PATHINFO_EXTENSION));
    
    $isImage = $type === 'image' || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']) || str_starts_with($mimeType ?? '', 'image/');
    $isPdf = $type === 'pdf' || $ext === 'pdf' || $mimeType === 'application/pdf';
    $isVideo = $type === 'video' || in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv']) || str_starts_with($mimeType ?? '', 'video/');
    $isPpt = $type === 'ppt' || in_array($ext, ['ppt', 'pptx']);
    $isWord = in_array($ext, ['doc', 'docx']);
    $isExcel = in_array($ext, ['xls', 'xlsx', 'csv']);
    $isArchive = in_array($ext, ['zip', 'rar', '7z']);
    $isYoutube = !empty($youtubeId);
    $isLink = $type === 'link' || ($url && !$isYoutube && filter_var($url, FILTER_VALIDATE_URL));
@endphp

<div class="space-y-4">
    {{-- 1. YOUTUBE VIDEO EMBED --}}
    @if($isYoutube)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-900 shadow-sm">
            <div class="relative w-full pt-[56.25%]">
                <iframe class="absolute inset-0 h-full w-full" src="https://www.youtube.com/embed/{{ $youtubeId }}" allowfullscreen title="YouTube Video Player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
            <div class="flex items-center justify-between bg-slate-800 p-3 px-4 text-xs text-slate-200">
                <span class="flex items-center gap-2 font-medium text-rose-400">
                    <i class="bi bi-youtube text-lg"></i> Pemutar Video YouTube
                </span>
                @if($url)
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1.5 font-bold text-white transition hover:bg-rose-700">
                    <i class="bi bi-box-arrow-up-right"></i> Buka di YouTube
                </a>
                @endif
            </div>
        </div>

    {{-- 2. FOTO / GAMBAR PREVIEW + DOWNLOAD --}}
    @elseif($isImage && $fileUrl)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shadow-sm">
            <div class="p-3 bg-slate-100/90 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                    <i class="bi bi-image text-emerald-600 text-base"></i> Pratinjau Gambar {{ $fileName ? "($fileName)" : '' }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50">
                        <i class="bi bi-arrows-angle-expand"></i> Tab Baru
                    </a>
                    <a href="{{ $fileUrl }}" download="{{ $fileName ?? 'gambar' }}" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1 text-xs font-bold text-white shadow-2xs transition hover:bg-indigo-700">
                        <i class="bi bi-download"></i> Unduh Gambar {{ $fileSize ? "($fileSize)" : '' }}
                    </a>
                </div>
            </div>
            <div class="flex items-center justify-center p-4 bg-slate-900/5">
                <img src="{{ $fileUrl }}" alt="{{ $fileName ?? 'Pratinjau Gambar' }}" class="max-h-[550px] w-auto max-w-full rounded-xl object-contain shadow-md border border-slate-200/60" loading="lazy">
            </div>
        </div>

    {{-- 3. PDF INLINE EMBED / READER + DOWNLOAD --}}
    @elseif($isPdf && $fileUrl)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="p-3 bg-slate-100/90 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-rose-600 text-base"></i> Dokumen PDF {{ $fileName ? "($fileName)" : '' }}
                </span>
                <div class="flex items-center gap-2">
                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50">
                        <i class="bi bi-box-arrow-up-right"></i> Tab Baru
                    </a>
                    <a href="{{ $fileUrl }}" download="{{ $fileName ?? 'dokumen.pdf' }}" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3 py-1 text-xs font-bold text-white shadow-2xs transition hover:bg-rose-700">
                        <i class="bi bi-download"></i> Unduh PDF {{ $fileSize ? "($fileSize)" : '' }}
                    </a>
                </div>
            </div>
            <div class="w-full h-[600px] bg-slate-100">
                <object data="{{ $fileUrl }}" type="application/pdf" class="w-full h-full">
                    <iframe src="{{ $fileUrl }}" class="w-full h-full border-0" title="PDF Reader">
                        <div class="p-6 text-center text-xs text-slate-500">
                            Browser Anda tidak mendukung preview PDF langsung. 
                            <a href="{{ $fileUrl }}" target="_blank" class="font-bold text-indigo-600 underline">Klik di sini untuk membuka PDF</a>.
                        </div>
                    </iframe>
                </object>
            </div>
        </div>

    {{-- 4. VIDEO FILE PLAYER + FULLSCREEN + DOWNLOAD --}}
    @elseif($isVideo && $fileUrl)
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 shadow-sm">
            <div class="p-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between text-white">
                <span class="text-xs font-bold flex items-center gap-2">
                    <i class="bi bi-film text-purple-400 text-base"></i> Pemutar Video {{ $fileName ? "($fileName)" : '' }}
                </span>
                <a href="{{ $fileUrl }}" download="{{ $fileName ?? 'video' }}" class="inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-1 text-xs font-bold text-white shadow-2xs transition hover:bg-purple-700">
                    <i class="bi bi-download"></i> Unduh Video {{ $fileSize ? "($fileSize)" : '' }}
                </a>
            </div>
            <div class="flex items-center justify-center p-2 bg-black">
                <video controls class="w-full max-h-[500px] rounded-xl focus:outline-none" preload="metadata">
                    <source src="{{ $fileUrl }}" type="{{ $mimeType ?: 'video/mp4' }}">
                    Browser Anda tidak mendukung pemutaran video ini.
                </video>
            </div>
        </div>

    {{-- 5. POWERPOINT & DOKUMEN OFFICE LAIN --}}
    @elseif(($isPpt || $isWord || $isExcel || $isArchive || $filePath) && $fileUrl)
        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl 
                        {{ $isPpt ? 'bg-orange-100 text-orange-600' : ($isWord ? 'bg-blue-100 text-blue-600' : ($isExcel ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-700')) }} text-2xl">
                        <i class="bi {{ $isPpt ? 'bi-file-earmark-ppt-fill' : ($isWord ? 'bi-file-earmark-word-fill' : ($isExcel ? 'bi-file-earmark-excel-fill' : ($isArchive ? 'bi-file-earmark-zip-fill' : 'bi-file-earmark-text-fill'))) }}"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-sm mb-0.5">{{ $fileName ?? 'File Lampiran Dokumen' }}</h4>
                        <p class="text-xs text-slate-500 mb-0 flex items-center gap-2">
                            <span>{{ strtoupper($ext ?: 'FILE') }}</span>
                            @if($fileSize) <span>&bull; {{ $fileSize }}</span> @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ $fileUrl }}" download="{{ $fileName ?? 'lampiran' }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-extrabold text-white shadow-2xs transition hover:bg-indigo-700">
                        <i class="bi bi-download text-sm"></i> Unduh File {{ $fileSize ? "($fileSize)" : '' }}
                    </a>
                </div>
            </div>
        </div>

    {{-- 6. TAUTAN / LINK BIASA --}}
    @elseif($isLink && $url)
        <div class="rounded-2xl border border-slate-200 bg-brand-50/50 p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white text-lg">
                        <i class="bi bi-link-45deg"></i>
                    </div>
                    <div>
                        <span class="block font-bold text-slate-800 text-xs">Tautan Eksternal</span>
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-brand-600 hover:underline truncate max-w-md block">
                            {{ $url }}
                        </a>
                    </div>
                </div>
                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-xs font-extrabold text-white shadow-2xs transition hover:bg-brand-700 shrink-0">
                    <i class="bi bi-box-arrow-up-right"></i> Buka Tautan (Tab Baru)
                </a>
            </div>
        </div>
    @endif
</div>
