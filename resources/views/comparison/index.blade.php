<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header
                title="Bandingkan Toko Tokopedia"
                subtitle="Cari produk dan bandingkan toko Anda dengan kompetitor secara langsung."
                illustration="komparasi.png"
            />

            @if ($errors->any())
                <div class="bg-coral-50 border border-coral-200 text-coral-700 px-4 py-3 rounded-lg mb-6 text-sm font-medium dark:bg-coral-900/20 dark:border-coral-900 dark:text-coral-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="card">
                <div class="p-6 sm:p-8">
                    <form action="{{ route('comparison.compare') }}" method="POST" class="space-y-6" id="compare-form">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="shop1_id" :value="__('Toko Dikelola')" />
                                <select name="shop1_id" id="shop1_id" class="field-select" required>
                                    <option value="">Pilih toko dikelola</option>
                                    @foreach($managedShops as $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="shop2_id" :value="__('Toko Saingan')" />
                                <select name="shop2_id" id="shop2_id" class="field-select" required>
                                    <option value="">Pilih toko saingan</option>
                                    @foreach($competitorShops as $shop)
                                        <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="keyword" :value="__('Kata Kunci Pencarian')" />
                            <x-text-input id="keyword" class="block mt-1 w-full" type="text" name="keyword" required
                                placeholder="contoh: Kaos Kaki" list="keyword-suggestions" autocomplete="off" />
                            @if ($recentKeywords->isNotEmpty())
                                <datalist id="keyword-suggestions">
                                    @foreach ($recentKeywords as $recentKeyword)
                                        <option value="{{ $recentKeyword }}"></option>
                                    @endforeach
                                </datalist>
                                <p class="text-xs text-ink-400 mt-1.5">Pencarian terakhir: {{ $recentKeywords->take(5)->implode(', ') }}</p>
                            @endif
                        </div>

                        <div>
                            <x-input-label for="sort" :value="__('Urutkan Berdasarkan')" />
                            <select name="sort" id="sort" class="field-select">
                                <option value="23">Relevan</option>
                                <option value="2">Terbaru</option>
                                <option value="10">Harga Tertinggi</option>
                                <option value="9">Harga Terendah</option>
                                <option value="11">Ulasan Terbanyak</option>
                                <option value="8">Pembelian Terbanyak</option>
                                <option value="5">Dilihat Terbanyak</option>
                                <option value="3">Pembaruan Terakhir</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('comparison.history') }}" class="btn-secondary">
                                <span class="icon icon-sm">history</span>
                                Riwayat Perbandingan
                            </a>
                            <button type="submit" id="compare-submit-btn" class="btn-primary">
                                <span class="icon icon-sm" id="compare-submit-icon">compare_arrows</span>
                                <span id="compare-submit-text">Bandingkan Toko</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tampilkan indikator loading saat proses scraping berjalan (bisa memakan waktu beberapa detik)
        document.getElementById('compare-form').addEventListener('submit', function () {
            const btn = document.getElementById('compare-submit-btn');
            const icon = document.getElementById('compare-submit-icon');
            const text = document.getElementById('compare-submit-text');
            btn.disabled = true;
            icon.classList.add('animate-spin');
            icon.textContent = 'progress_activity';
            text.textContent = 'Mengambil data toko...';
        });
    </script>
</x-app-layout>
