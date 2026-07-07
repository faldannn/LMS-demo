@props(['commentableType', 'commentableId', 'comments'])

<div class="mt-8 overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70">
    <div class="border-b border-slate-200 bg-slate-50/80 px-6 py-4">
        <h3 class="flex items-center gap-2 text-base font-bold text-slate-800">
            <i class="bi bi-chat-dots text-brand-600"></i>Komentar ({{ $comments->count() }})
        </h3>
    </div>
    <div class="p-6">
        <form action="{{ route('comments.store', [$commentableType, $commentableId]) }}" method="POST" class="mb-8">
            @csrf
            <div class="relative">
                <textarea name="body" rows="3" class="block w-full resize-none rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition-colors focus:border-brand-500 focus:ring-1 focus:ring-brand-500 @error('body') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror" placeholder="Tulis komentar atau pertanyaan Anda di sini..." required>{{ old('body') }}</textarea>
                <div class="absolute bottom-3 right-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
                        <i class="bi bi-send-fill"></i>Kirim
                    </button>
                </div>
            </div>
            @error('body')<p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>@enderror
        </form>

        <div class="flex flex-col gap-4">
            @forelse($comments as $comment)
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-colors hover:bg-slate-50">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&color=15803d&background=dcfce7' }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white" alt="Avatar">
                        <div>
                            <div class="flex items-center gap-2">
                                <strong class="text-sm font-bold text-slate-900">{{ $comment->user->name }}</strong>
                                @if($comment->user->isGuru())
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700 ring-1 ring-blue-200">GURU</span>
                                @elseif($comment->user->isSiswa())
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-1.5 py-0.5 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200">SISWA</span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @can('delete', $comment)
                    <form id="del-comment-{{ $comment->id }}" action="{{ route('comments.destroy', $comment) }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                    <button type="button" class="text-xs font-semibold text-rose-500 transition hover:text-rose-700" data-confirm="Hapus komentar ini?" data-form="del-comment-{{ $comment->id }}">
                        Hapus
                    </button>
                    @endcan
                </div>
                
                <p class="mt-3 pl-11 text-sm leading-relaxed text-slate-700">{{ $comment->body }}</p>
                
                @foreach($comment->children as $reply)
                <div class="mt-4 ml-11 border-l-2 border-slate-200 pl-4">
                    <div class="flex items-center gap-2">
                        <strong class="text-sm font-bold text-slate-900">{{ $reply->user->name }}</strong>
                        <span class="text-xs text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $reply->body }}</p>
                </div>
                @endforeach
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="bi bi-chat-square text-xl"></i>
                </div>
                <p class="mt-3 text-sm font-medium text-slate-500">Belum ada komentar.</p>
                <p class="text-xs text-slate-400">Jadilah yang pertama untuk memulai diskusi!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
