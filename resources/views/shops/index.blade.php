<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header
                title="Manajemen Toko"
                subtitle="Kelola daftar toko yang dikelola dan kompetitor Anda."
                illustration="manajemen-toko.png"
            />
            <div class="card overflow-hidden">
                <div class="p-6">
                    @if (session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative mb-4 text-sm font-medium dark:bg-mint-900/20 dark:border-mint-900 dark:text-mint-300" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <a href="{{ route('shops.create') }}" class="btn-primary shrink-0">
                            <span class="icon icon-sm">add</span>
                            Tambah Toko
                        </a>
                        <div class="flex w-full md:w-auto">
                            <input type="text" id="shop-search" placeholder="Cari nama toko..."
                                   class="field-input rounded-r-none w-full md:w-64" oninput="filterShops(this.value)">
                            <button type="button" class="btn-primary rounded-l-none cursor-default">
                                <span class="icon icon-sm">search</span>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-ink-100 dark:border-ink-800">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Nama Toko</th>
                                    <th>Link</th>
                                    <th>Jenis</th>
                                    <th>Ditambahkan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="shops-table-body">
                                @forelse ($shops as $shop)
                                    <tr class="shop-row" data-name="{{ strtolower($shop->name) }}">
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <span class="avatar-coral h-9 w-9 text-xs">{{ strtoupper(substr($shop->name, 0, 2)) }}</span>
                                                <span class="font-medium text-ink-900 dark:text-white">{{ $shop->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ $shop->link }}" target="_blank" class="text-ink-500 hover:text-brand-600 truncate block max-w-xs">{{ $shop->link }}</a>
                                        </td>
                                        <td>
                                            <span class="{{ $shop->jenis == 'dikelola' ? 'badge-success' : 'badge-brand' }}">
                                                {{ ucfirst($shop->jenis) }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap text-ink-400">
                                            {{ $shop->created_at->diffForHumans() }}
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('shops.edit', $shop) }}" class="text-ink-500 hover:text-brand-600 font-medium">Edit</a>
                                                <form action="{{ route('shops.destroy', $shop) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-coral-600 hover:text-coral-700 font-medium" onclick="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-ink-400 py-8">Belum ada toko yang ditambahkan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <p id="shop-search-empty" class="hidden text-center text-ink-400 py-8">Tidak ada toko yang cocok dengan pencarian.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterShops(query) {
            const q = query.trim().toLowerCase();
            const rows = document.querySelectorAll('#shops-table-body .shop-row');
            let visibleCount = 0;
            rows.forEach(row => {
                const match = row.dataset.name.includes(q);
                row.classList.toggle('hidden', !match);
                if (match) visibleCount++;
            });
            document.getElementById('shop-search-empty').classList.toggle('hidden', visibleCount !== 0 || rows.length === 0);
        }
    </script>
</x-app-layout>
