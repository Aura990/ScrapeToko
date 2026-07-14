<x-app-layout>
    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header
                title="Riwayat Perbandingan"
                subtitle="Daftar perbandingan toko yang pernah dilakukan."
                illustration="riwayat.png"
            />

            <div class="card overflow-hidden">
                <div class="p-5 border-b border-ink-100 dark:border-ink-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <form method="GET" class="flex w-full sm:w-auto">
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                               placeholder="Cari berdasarkan kata kunci..." class="field-input rounded-r-none w-full sm:w-64">
                        <button type="submit" class="btn-primary rounded-l-none">
                            <span class="icon icon-sm">search</span>
                        </button>
                    </form>
                    <a href="{{ route('comparison.index') }}" class="btn-primary shrink-0">
                        <span class="icon icon-sm">compare_arrows</span>
                        Perbandingan Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Kata Kunci</th>
                                <th>Toko Dikelola</th>
                                <th>Toko Saingan</th>
                                <th>Termurah</th>
                                <th>Oleh</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($histories as $history)
                                <tr>
                                    <td class="font-medium text-ink-900 dark:text-white">{{ $history->keyword }}</td>
                                    <td>
                                        {{ $history->shop1?->name ?? 'Toko dihapus' }}
                                        @if (! is_null($history->shop1_min_price))
                                            <div class="text-xs text-ink-400">mulai Rp {{ number_format($history->shop1_min_price, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $history->shop2?->name ?? 'Toko dihapus' }}
                                        @if (! is_null($history->shop2_min_price))
                                            <div class="text-xs text-ink-400">mulai Rp {{ number_format($history->shop2_min_price, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($history->cheaper_shop === 'shop1')
                                            <span class="badge-success">{{ $history->shop1?->name ?? 'Toko dikelola' }}</span>
                                        @elseif ($history->cheaper_shop === 'shop2')
                                            <span class="badge-coral">{{ $history->shop2?->name ?? 'Toko saingan' }}</span>
                                        @elseif ($history->cheaper_shop === 'sama')
                                            <span class="badge-neutral">Sama</span>
                                        @else
                                            <span class="badge-neutral">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $history->user?->name ?? 'Sistem' }}</td>
                                    <td class="whitespace-nowrap">{{ $history->created_at->diffForHumans() }}</td>
                                    <td>
                                        @if ($history->shop1 && $history->shop2)
                                            <form action="{{ route('comparison.compare') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="shop1_id" value="{{ $history->shop1_id }}">
                                                <input type="hidden" name="shop2_id" value="{{ $history->shop2_id }}">
                                                <input type="hidden" name="keyword" value="{{ $history->keyword }}">
                                                <input type="hidden" name="sort" value="{{ $history->sort }}">
                                                <button type="submit" class="text-brand-600 hover:text-brand-700 font-medium text-sm dark:text-brand-400">
                                                    Ulangi
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-ink-300 text-sm">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="flex flex-col items-center justify-center text-center py-12 text-ink-400">
                                            <span class="icon icon-lg mb-2">history</span>
                                            <p class="text-sm">Belum ada riwayat perbandingan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($histories->hasPages())
                    <div class="px-5 py-4 border-t border-ink-100 dark:border-ink-800">
                        {{ $histories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
