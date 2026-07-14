<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="page-title">Notifikasi</h1>
                    <p class="page-subtitle">Peringatan harga kompetitor dan aktivitas lainnya.</p>
                </div>
                @if ($notifications->where('read_at', null)->isNotEmpty())
                    <form action="{{ route('notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-secondary shrink-0">
                            <span class="icon icon-sm">done_all</span>
                            Tandai Semua Dibaca
                        </button>
                    </form>
                @endif
            </div>

            <div class="card divide-y divide-ink-100 dark:divide-ink-800 overflow-hidden">
                @forelse ($notifications as $notification)
                    <div class="p-5 flex items-start gap-4 {{ is_null($notification->read_at) ? 'bg-brand-50/40 dark:bg-brand-900/10' : '' }}">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coral-50 text-coral-500 dark:bg-coral-900/30 dark:text-coral-300 shrink-0">
                            <span class="icon icon-md">payments</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-ink-800 dark:text-ink-200 text-sm">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                            <p class="text-xs text-ink-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @if (is_null($notification->read_at))
                            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center text-center py-14 text-ink-400">
                        <span class="icon icon-lg mb-2">notifications_none</span>
                        <p class="text-sm">Belum ada notifikasi.</p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->hasPages())
                <div class="mt-6">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
