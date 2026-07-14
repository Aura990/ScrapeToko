<x-app-layout>
    @php
        // Hitung harga termurah per toko untuk memberi badge "Termurah" pada kartu produk
        $cheapestPrices = [];
        foreach (['shop1_data', 'shop2_data'] as $sd) {
            $prices = collect(${$sd}['products'])
                ->map(function ($p) {
                    $digits = preg_replace('/[^0-9]/', '', $p['price'] ?? '');
                    return $digits === '' ? null : (int) $digits;
                })
                ->filter(fn ($v) => $v !== null);
            $cheapestPrices[$sd] = $prices->isEmpty() ? null : $prices->min();
        }
    @endphp

    <div class="py-10">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-8">
                <div>
                    <h1 class="page-title text-2xl md:text-3xl font-extrabold text-ink-900 dark:text-white tracking-tight">Hasil Perbandingan Toko</h1>
                    <p class="page-subtitle text-ink-500 mt-1 dark:text-ink-400">Cari produk dan bandingkan harga dari berbagai toko dengan kompetitor secara langsung.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('comparison.index') }}" class="btn-secondary">
                        <span class="icon icon-sm">arrow_back</span>
                        Kembali
                    </a>
                    <select id="local-sort" onchange="applyLocalSort()" class="field-select py-2.5 w-36">
                        <option value="default">Relevan</option>
                        <option value="price-asc">Harga Termurah</option>
                        <option value="price-desc">Harga Termahal</option>
                    </select>
                    
                    <!-- Export CSV Button styled to match mockup -->
                    <button type="button" onclick="exportComparisonCSV()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-brand-500 bg-white hover:bg-brand-50 text-ink-800 font-semibold text-sm transition shadow-soft">
                        <span class="icon icon-sm text-emerald-600">description</span>
                        Export CSV
                    </button>
                    
                    <!-- Export PDF Button styled to match mockup -->
                    <form action="{{ route('comparison.export-pdf') }}" method="POST" class="inline" id="export-pdf-form">
                        @csrf
                        <input type="hidden" name="shop1_id" value="{{ $shop1->id }}">
                        <input type="hidden" name="shop2_id" value="{{ $shop2->id }}">
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        <input type="hidden" name="page1" value="{{ $shop1_data['current_page'] }}">
                        <input type="hidden" name="page2" value="{{ $shop2_data['current_page'] }}">
                        <input type="hidden" name="sort" value="{{ $current_sort }}">
                        <button type="submit" id="export-pdf-btn" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-brand-500 bg-white hover:bg-brand-50 text-ink-800 font-semibold text-sm transition shadow-soft">
                            <span class="icon icon-sm text-rose-600" id="export-pdf-icon">picture_as_pdf</span>
                            <span id="export-pdf-text">Export PDF</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_420px] gap-6 items-start">
                @foreach (['shop1_data', 'shop2_data'] as $index => $shop_data)
                    <div class="card overflow-hidden">
                        <!-- Shop Header -->
                        <div class="p-5 border-b border-ink-100 dark:border-ink-800 flex justify-between items-center gap-3">
                            <div class="min-w-0">
                                <h2 class="text-lg font-bold text-ink-900 dark:text-white flex items-center gap-1.5 truncate">
                                    {{ ${$shop_data}['shop_name'] }}
                                    <span class="icon text-brand-600 text-base flex-shrink-0">check_circle</span>
                                </h2>
                                <p class="text-sm text-ink-400">{{ count(${$shop_data}['products']) }} produk ditemukan</p>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                @if (${$shop_data}['prev_page'])
                                    <form action="{{ route('comparison.compare') }}" method="POST" class="inline compare-nav-form">
                                        @csrf
                                        <input type="hidden" name="shop1_id" value="{{ $shop1->id }}">
                                        <input type="hidden" name="shop2_id" value="{{ $shop2->id }}">
                                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                                        <input type="hidden" name="{{ $index === 0 ? 'page1' : 'page2' }}" value="{{ ${$shop_data}['current_page'] - 1 }}">
                                        <input type="hidden" name="sort" value="{{ $current_sort }}">
                                        <button type="submit" class="flex items-center justify-center h-8 w-8 rounded-lg text-ink-500 hover:bg-ink-100 dark:text-ink-400 dark:hover:bg-ink-800 transition">
                                            <span class="icon icon-sm">chevron_left</span>
                                        </button>
                                    </form>
                                @endif
                                @if (${$shop_data}['next_page'])
                                    <form action="{{ route('comparison.compare') }}" method="POST" class="inline compare-nav-form">
                                        @csrf
                                        <input type="hidden" name="shop1_id" value="{{ $shop1->id }}">
                                        <input type="hidden" name="shop2_id" value="{{ $shop2->id }}">
                                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                                        <input type="hidden" name="{{ $index === 0 ? 'page1' : 'page2' }}" value="{{ ${$shop_data}['current_page'] + 1 }}">
                                        <input type="hidden" name="sort" value="{{ $current_sort }}">
                                        <button type="submit" class="flex items-center justify-center h-8 w-8 rounded-lg text-brand-600 hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-brand-900/30 transition">
                                            <span class="icon icon-sm">chevron_right</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Product List Grid -->
                        <div class="p-5">
                            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4" id="product-grid-{{ $index }}">
                                @forelse (${$shop_data}['products'] as $pIdx => $product)
                                    @php
                                        $digits = preg_replace('/[^0-9]/', '', $product['price'] ?? '');
                                        $parsedPrice = $digits === '' ? null : (int) $digits;
                                        $isCheapest = $parsedPrice !== null && $parsedPrice === $cheapestPrices[$shop_data];
                                    @endphp
                                    <label class="group cursor-pointer product-card-container relative block" 
                                           id="card-{{ $index }}-{{ $pIdx }}"
                                           data-price="{{ $parsedPrice ?? '' }}"
                                           onclick="selectProductCard({{ $index }}, {{ $pIdx }}, {{ json_encode($product) }})">
                                        
                                        <!-- Selection radio input (hidden) -->
                                        <input type="radio" name="selected_product_{{ $index }}"
                                               id="radio-{{ $index }}-{{ $pIdx }}"
                                               value="{{ json_encode($product) }}"
                                               class="hidden">

                                        <!-- Outer Flex Container -->
                                        <div class="flex gap-3 p-3 bg-white dark:bg-ink-900 border border-ink-100 dark:border-ink-800 rounded-2xl shadow-soft hover:shadow-card hover:border-brand-300 transition duration-150 relative card-inner-box">
                                            
                                            <!-- Checkmark Indicator -->
                                            <span class="icon text-brand-600 absolute top-2 right-2 checkmark-icon hidden" style="font-size: 20px;">check_circle</span>

                                            <!-- Left: Image Wrapper -->
                                            <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl overflow-hidden bg-surface dark:bg-ink-800 p-1 flex items-center justify-center border border-ink-50 dark:border-ink-800">
                                                <img src="{{ $product['image'] }}" alt="{{ $product['title'] }}"
                                                     class="max-w-full max-h-full object-contain select-none pointer-events-none group-hover:scale-105 transition duration-150">
                                            </div>

                                            <!-- Right: Info Wrapper -->
                                            <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                                                <div class="space-y-0.5">
                                                    <!-- Badges Row -->
                                                    <div class="flex flex-wrap items-center gap-1.5 dynamic-badges-row">
                                                        @if ($index === 0)
                                                            <span class="badge bg-brand-50 text-brand-600 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase inline-block">Produk Anda</span>
                                                        @elseif ($isCheapest)
                                                            <span class="badge bg-mint-50 text-mint-700 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase inline-block">Termurah</span>
                                                        @endif
                                                    </div>

                                                    <!-- Title -->
                                                    <h4 class="text-xs sm:text-sm font-semibold text-ink-800 dark:text-ink-200 line-clamp-2 leading-tight group-hover:text-brand-600 transition">
                                                        {{ $product['title'] }}
                                                    </h4>
                                                </div>

                                                <!-- Price & Rating Row -->
                                                <div class="mt-1">
                                                    <p class="text-sm sm:text-base font-extrabold text-brand-600 dark:text-brand-400 price-text">
                                                        {{ $product['price'] }}
                                                    </p>
                                                    
                                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5 text-[10px] text-ink-400">
                                                        @if(isset($product['rating']))
                                                            <div class="flex items-center gap-0.5">
                                                                <span class="icon text-amber-400" style="font-size:12px">star</span>
                                                                <span>{{ $product['rating'] }}</span>
                                                            </div>
                                                        @endif
                                                        @if(isset($product['sold']))
                                                            <span>{{ $product['sold'] }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @empty
                                    <p class="col-span-2 text-center text-ink-400 py-8">Tidak ada produk ditemukan untuk toko ini.</p>
                                @endforelse
                            </div>

                            <!-- Premium Client Pagination Bar -->
                            <div class="flex items-center justify-between mt-6 pt-4 border-t border-ink-100 dark:border-ink-800" id="pagination-container-{{ $index }}">
                                <button type="button" id="page-prev-{{ $index }}" onclick="changePage({{ $index }}, -1)"
                                        class="flex items-center justify-center h-9 w-9 rounded-xl border border-ink-200 text-ink-600 hover:bg-ink-50 disabled:opacity-40 disabled:cursor-not-allowed dark:border-ink-700 dark:text-ink-400 dark:hover:bg-ink-800 transition">
                                    <span class="icon icon-sm">chevron_left</span>
                                </button>
                                
                                <div class="flex items-center gap-1.5" id="page-numbers-{{ $index }}">
                                    <!-- Dynamic page numbers filled by JS -->
                                </div>
                                
                                <button type="button" id="page-next-{{ $index }}" onclick="changePage({{ $index }}, 1)"
                                        class="flex items-center justify-center h-9 w-9 rounded-xl border border-ink-200 text-ink-600 hover:bg-ink-50 disabled:opacity-40 disabled:cursor-not-allowed dark:border-ink-700 dark:text-ink-400 dark:hover:bg-ink-800 transition">
                                    <span class="icon icon-sm">chevron_right</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Side Comparison Sticky Card -->
                <div class="card p-5 lg:sticky lg:top-24">
                    <div class="flex items-center justify-between mb-5 border-b border-ink-100 dark:border-ink-800 pb-3">
                        <h3 id="selected-count-header" class="text-base font-bold text-ink-900 dark:text-white">0 produk dipilih</h3>
                        <span class="icon text-ink-400">keyboard_arrow_up</span>
                    </div>

                    <!-- Empty State -->
                    <div id="comparison-empty" class="flex flex-col items-center justify-center text-center py-10 text-ink-400">
                        <span class="icon icon-lg mb-2 text-brand-500">compare_arrows</span>
                        <p class="text-sm font-medium">Pilih satu produk dari masing-masing toko untuk melihat perbandingan harga.</p>
                    </div>

                    <!-- Content State -->
                    <div id="comparison-content" class="hidden space-y-5">
                        <!-- Product 1 Details -->
                        <div id="product1-details" class="flex gap-4 p-4 bg-surface dark:bg-ink-800 rounded-2xl border border-ink-100 dark:border-ink-700">
                            <!-- Dynamic product 1 content -->
                        </div>

                        <!-- VS Divider -->
                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-ink-100 dark:border-ink-800"></div>
                            <span class="flex-shrink mx-4 text-[10px] font-bold bg-brand-50 text-brand-600 dark:bg-brand-900/40 dark:text-brand-300 h-6 w-6 flex items-center justify-center rounded-full border border-brand-200">VS</span>
                            <div class="flex-grow border-t border-ink-100 dark:border-ink-800"></div>
                        </div>

                        <!-- Product 2 Details -->
                        <div id="product2-details" class="flex gap-4 p-4 bg-surface dark:bg-ink-800 rounded-2xl border border-ink-100 dark:border-ink-700">
                            <!-- Dynamic product 2 content -->
                        </div>

                        <!-- Pricing Details Summary -->
                        <div class="border-t border-ink-100 dark:border-ink-800 pt-4 space-y-3">
                            <!-- Selisih Harga -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-ink-100 dark:border-ink-800">
                                <span class="icon icon-md text-mint-500 bg-mint-50 p-2 rounded-lg">local_offer</span>
                                <div>
                                    <span class="text-ink-400 text-xs block">Selisih Harga</span>
                                    <span id="price-diff" class="font-bold text-mint-600 dark:text-mint-400 text-sm"></span>
                                </div>
                            </div>

                            <!-- Persentase Selisih -->
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-ink-100 dark:border-ink-800">
                                <span class="icon icon-md text-mint-500 bg-mint-50 p-2 rounded-lg" id="trend-icon-container">trending_down</span>
                                <div>
                                    <span class="text-ink-400 text-xs block">Persentase Selisih</span>
                                    <span id="price-percentage" class="font-bold text-mint-600 dark:text-mint-400 text-sm"></span>
                                </div>
                            </div>

                            <!-- Trophy Recommendation Banner -->
                            <div id="recommendation-banner" class="flex items-center gap-3 p-3.5 bg-mint-50 text-mint-700 rounded-xl font-semibold text-xs border border-mint-200">
                                <span class="icon text-mint-600 text-lg">emoji_events</span>
                                <span id="recommendation-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay loading saat pindah halaman / ganti urutan -->
    <div id="nav-loading-overlay" class="hidden fixed inset-0 z-50 bg-ink-950/40 backdrop-blur-sm flex items-center justify-center">
        <div class="card px-6 py-4 flex items-center gap-3">
            <span class="icon icon-md animate-spin text-brand-600">progress_activity</span>
            <span class="font-semibold text-ink-700 dark:text-ink-200">Memuat data...</span>
        </div>
    </div>

    <!-- JavaScript untuk handling perbandingan -->
    <script>
        let selectedProducts = [null, null];
        let selectedCards = [null, null];
        const emptyState = document.getElementById('comparison-empty');
        const contentState = document.getElementById('comparison-content');

        function selectProductCard(index, pIdx, product) {
            // Check radio input
            const radio = document.getElementById(`radio-${index}-${pIdx}`);
            if (radio) {
                radio.checked = true;
            }

            selectedCards[index] = pIdx;
            selectedProducts[index] = product;

            updateSelectedCardBorders();
            updateSelectedCount();
            updateComparisonPanel();
            updateRelativeComparisonBadges();
        }

        function updateSelectedCardBorders() {
            [0, 1].forEach(index => {
                const grid = document.getElementById(`product-grid-${index}`);
                if (!grid) return;
                const containers = grid.querySelectorAll('.product-card-container');
                containers.forEach(container => {
                    const innerBox = container.querySelector('.card-inner-box');
                    const checkmark = container.querySelector('.checkmark-icon');
                    if (innerBox) {
                        innerBox.classList.remove('border-brand-500', 'border-2', 'bg-brand-50/10');
                        innerBox.classList.add('border-ink-100', 'dark:border-ink-800');
                    }
                    if (checkmark) {
                        checkmark.classList.add('hidden');
                    }
                });

                const selectedIdx = selectedCards[index];
                if (selectedIdx !== null) {
                    const container = document.getElementById(`card-${index}-${selectedIdx}`);
                    if (container) {
                        const innerBox = container.querySelector('.card-inner-box');
                        const checkmark = container.querySelector('.checkmark-icon');
                        if (innerBox) {
                            innerBox.classList.remove('border-ink-100', 'dark:border-ink-800');
                            innerBox.classList.add('border-brand-500', 'border-2', 'bg-brand-50/10');
                        }
                        if (checkmark) {
                            checkmark.classList.remove('hidden');
                        }
                    }
                }
            });
        }

        function updateSelectedCount() {
            const count = selectedProducts.filter(p => p !== null).length;
            document.getElementById('selected-count-header').textContent = `${count} produk dipilih`;
        }

        function renderMiniProduct1(product) {
            return `
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl overflow-hidden bg-white dark:bg-ink-900 p-1.5 flex items-center justify-center border border-ink-100 dark:border-ink-800">
                    <img src="${product.image}" alt="${product.title}" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                    <div>
                        <span class="badge bg-brand-50 text-brand-600 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase mb-1 inline-block">Produk Anda</span>
                        <h4 class="text-sm font-bold text-ink-800 dark:text-ink-200 line-clamp-2 leading-tight">${product.title}</h4>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between gap-2 flex-wrap">
                        <p class="text-brand-600 dark:text-brand-400 font-extrabold text-base sm:text-lg">${product.price}</p>
                        <a href="${product.link}" target="_blank" class="text-brand-500 hover:text-brand-600 text-xs font-semibold inline-flex items-center gap-0.5">
                            Lihat detail <span class="icon" style="font-size:14px">arrow_forward</span>
                        </a>
                    </div>
                </div>
            `;
        }

        function renderMiniProduct2(product, shopName, isCheaper) {
            const badgeClass = isCheaper ? 'bg-mint-50 text-mint-700' : 'bg-coral-50 text-coral-700';
            const badgeText = isCheaper ? 'TERMURAH' : 'LEBIH MAHAL';
            const priceClass = isCheaper ? 'text-mint-600 dark:text-mint-400' : 'text-coral-500 dark:text-coral-400';
            return `
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-xl overflow-hidden bg-white dark:bg-ink-900 p-1.5 flex items-center justify-center border border-ink-100 dark:border-ink-800">
                    <img src="${product.image}" alt="${product.title}" class="max-w-full max-h-full object-contain">
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-between py-1">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                            <span class="badge ${badgeClass} text-[10px] px-2 py-0.5 rounded-full font-bold uppercase inline-block">${badgeText}</span>
                            <span class="text-[10px] text-ink-400 font-bold uppercase tracking-wider">${shopName}</span>
                        </div>
                        <h4 class="text-sm font-bold text-ink-800 dark:text-ink-200 line-clamp-2 leading-tight">${product.title}</h4>
                    </div>
                    <div class="mt-2 flex items-baseline justify-between gap-2 flex-wrap">
                        <p class="${priceClass} font-extrabold text-base sm:text-lg">${product.price}</p>
                        <a href="${product.link}" target="_blank" class="text-brand-500 hover:text-brand-600 text-xs font-semibold inline-flex items-center gap-0.5">
                            Lihat detail <span class="icon" style="font-size:14px">arrow_forward</span>
                        </a>
                    </div>
                </div>
            `;
        }

        function updateComparisonPanel() {
            const p1 = selectedProducts[0];
            const p2 = selectedProducts[1];
            if (p1 && p2) {
                const price1 = parseInt(p1.price.replace(/[^0-9]/g, '')) || 0;
                const price2 = parseInt(p2.price.replace(/[^0-9]/g, '')) || 0;
                const difference = Math.abs(price1 - price2);
                const percentage = price1 === 0 && price2 === 0 ? 0 : ((difference / Math.max(price1, price2)) * 100).toFixed(2);
                
                const isCheaper = price2 <= price1;
                const shop1Name = @json($shop1_data['shop_name']);
                const shop2Name = @json($shop2_data['shop_name']);

                document.getElementById('product1-details').innerHTML = renderMiniProduct1(p1);
                document.getElementById('product2-details').innerHTML = renderMiniProduct2(p2, shop2Name, isCheaper);

                document.getElementById('price-diff').textContent = 'Rp ' + difference.toLocaleString('id-ID');
                
                const diffText = isCheaper ? 'lebih murah' : 'lebih mahal';
                document.getElementById('price-percentage').textContent = `${percentage}% ${diffText}`;

                const trendContainer = document.getElementById('trend-icon-container');
                if (isCheaper) {
                    trendContainer.textContent = 'trending_down';
                    trendContainer.className = 'icon icon-md text-mint-500 bg-mint-50 p-2 rounded-lg';
                    document.getElementById('price-diff').className = 'font-bold text-mint-600 dark:text-mint-400 text-sm';
                    document.getElementById('price-percentage').className = 'font-bold text-mint-600 dark:text-mint-400 text-sm';
                } else {
                    trendContainer.textContent = 'trending_up';
                    trendContainer.className = 'icon icon-md text-coral-500 bg-coral-50 p-2 rounded-lg';
                    document.getElementById('price-diff').className = 'font-bold text-coral-500 dark:text-coral-400 text-sm';
                    document.getElementById('price-percentage').className = 'font-bold text-coral-500 dark:text-coral-400 text-sm';
                }

                // Trophy Banner
                const recommendationBanner = document.getElementById('recommendation-banner');
                const recommendationText = document.getElementById('recommendation-text');
                if (isCheaper) {
                    recommendationBanner.className = 'flex items-center gap-3 p-3.5 bg-mint-50 text-mint-700 rounded-xl font-semibold text-xs border border-mint-200';
                    recommendationText.textContent = `${shop2Name} adalah pilihan termurah!`;
                } else {
                    recommendationBanner.className = 'flex items-center gap-3 p-3.5 bg-brand-50 text-brand-700 rounded-xl font-semibold text-xs border border-brand-200';
                    recommendationText.textContent = `${shop1Name} adalah pilihan termurah!`;
                }

                emptyState.classList.add('hidden');
                contentState.classList.remove('hidden');
                document.getElementById('selected-count-header').textContent = '2 produk dipilih';
            } else {
                emptyState.classList.remove('hidden');
                contentState.classList.add('hidden');
                const count = selectedProducts.filter(p => p !== null).length;
                document.getElementById('selected-count-header').textContent = `${count} produk dipilih`;
            }
        }

        function updateRelativeComparisonBadges() {
            const p0 = selectedProducts[0];
            const p1 = selectedProducts[1];

            // If both are selected, we can compute relative differences
            if (p0 && p1) {
                const price0 = parseInt(p0.price.replace(/[^0-9]/g, '')) || 0;
                const price1 = parseInt(p1.price.replace(/[^0-9]/g, '')) || 0;

                // For all products in Shop 2 (index 1), compare against selected Product 0 (price0)
                const grid1 = document.getElementById('product-grid-1');
                if (grid1) {
                    const cards = grid1.querySelectorAll('.product-card-container');
                    cards.forEach(card => {
                        const price = parseInt(card.dataset.price) || 0;
                        const badgesRow = card.querySelector('.dynamic-badges-row');
                        const priceText = card.querySelector('.price-text');
                        
                        if (badgesRow && priceText && price0 > 0 && price > 0) {
                            let badgeHtml = '';
                            if (price < price0) {
                                const diff = price0 - price;
                                const pct = Math.round((diff / price0) * 100);
                                badgeHtml = `
                                    <span class="badge bg-mint-50 text-mint-700 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Termurah</span>
                                    <span class="text-mint-600 dark:text-mint-400 text-[10px] font-bold">&gt; ${pct}% lebih murah</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-mint-600 dark:text-mint-400 price-text';
                            } else if (price > price0) {
                                const diff = price - price0;
                                const pct = Math.round((diff / price0) * 100);
                                badgeHtml = `
                                    <span class="text-coral-500 dark:text-coral-400 text-[10px] font-bold">&gt; ${pct}% lebih mahal</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-coral-500 dark:text-coral-400 price-text';
                            } else {
                                badgeHtml = `
                                    <span class="text-ink-400 text-[10px] font-bold">Harga sama</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-ink-900 dark:text-white price-text';
                            }
                            badgesRow.innerHTML = badgeHtml;
                        }
                    });
                }

                // For all products in Shop 1 (index 0), compare against selected Product 1 (price1)
                const grid0 = document.getElementById('product-grid-0');
                if (grid0) {
                    const cards = grid0.querySelectorAll('.product-card-container');
                    cards.forEach(card => {
                        const price = parseInt(card.dataset.price) || 0;
                        const badgesRow = card.querySelector('.dynamic-badges-row');
                        const priceText = card.querySelector('.price-text');
                        
                        if (badgesRow && priceText && price1 > 0 && price > 0) {
                            let badgeHtml = '';
                            if (price < price1) {
                                const diff = price1 - price;
                                const pct = Math.round((diff / price1) * 100);
                                badgeHtml = `
                                    <span class="badge bg-mint-50 text-mint-700 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Termurah</span>
                                    <span class="text-mint-600 dark:text-mint-400 text-[10px] font-bold">&gt; ${pct}% lebih murah</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-mint-600 dark:text-mint-400 price-text';
                            } else if (price > price1) {
                                const diff = price - price1;
                                const pct = Math.round((diff / price1) * 100);
                                badgeHtml = `
                                    <span class="text-coral-500 dark:text-coral-400 text-[10px] font-bold">&gt; ${pct}% lebih mahal</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-coral-500 dark:text-coral-400 price-text';
                            } else {
                                badgeHtml = `
                                    <span class="badge bg-brand-50 text-brand-600 text-[9px] px-2 py-0.5 rounded-full font-bold uppercase">Produk Anda</span>
                                `;
                                priceText.className = 'text-sm sm:text-base font-extrabold text-brand-600 dark:text-brand-400 price-text';
                            }
                            badgesRow.innerHTML = badgeHtml;
                        }
                    });
                }
            }
        }

        // Sort tampilan lokal (tanpa reload) berdasarkan harga
        function applyLocalSort() {
            const mode = document.getElementById('local-sort').value;
            ['product-grid-0', 'product-grid-1'].forEach(gridId => {
                const grid = document.getElementById(gridId);
                if (!grid) return;
                const cards = Array.from(grid.querySelectorAll('.product-card-container'));

                if (mode === 'default') {
                    cards.sort((a, b) => a.dataset.originalOrder - b.dataset.originalOrder);
                } else {
                    cards.sort((a, b) => {
                        const priceA = a.dataset.price === '' ? Infinity : parseInt(a.dataset.price);
                        const priceB = b.dataset.price === '' ? Infinity : parseInt(b.dataset.price);
                        return mode === 'price-asc' ? priceA - priceB : priceB - priceA;
                    });
                }
                cards.forEach(card => grid.appendChild(card));
            });

            // Reset ke halaman pertama setiap kali urutan berubah
            gridPages = [1, 1];
            renderPage(0);
            renderPage(1);
        }

        // Simpan urutan asli agar bisa dikembalikan
        document.querySelectorAll('.product-card-container').forEach((card, i) => {
            card.dataset.originalOrder = i;
        });

        // === Pagination produk (client-side) supaya tidak scrolling panjang ===
        const PAGE_SIZE = 10;
        let gridPages = [1, 1];

        function renderPage(gridIndex) {
            const grid = document.getElementById('product-grid-' + gridIndex);
            if (!grid) return;
            const cards = Array.from(grid.querySelectorAll('.product-card-container'));
            const totalPages = Math.max(1, Math.ceil(cards.length / PAGE_SIZE));
            if (!gridPages[gridIndex] || gridPages[gridIndex] > totalPages) gridPages[gridIndex] = 1;
            const page = gridPages[gridIndex];

            cards.forEach((card, i) => {
                const cardPage = Math.floor(i / PAGE_SIZE) + 1;
                card.style.display = cardPage === page ? '' : 'none';
            });

            // Page numbers rendering
            const numbersContainer = document.getElementById('page-numbers-' + gridIndex);
            if (numbersContainer) {
                let numbersHtml = '';
                for (let p = 1; p <= totalPages; p++) {
                    if (p === page) {
                        numbersHtml += `
                            <button type="button" class="flex items-center justify-center h-9 w-9 rounded-xl bg-brand-600 text-white font-semibold text-sm shadow-soft">
                                ${p}
                            </button>
                        `;
                    } else {
                        numbersHtml += `
                            <button type="button" onclick="goToPage(${gridIndex}, ${p})" class="flex items-center justify-center h-9 w-9 rounded-xl border border-ink-200 text-ink-600 hover:bg-ink-50 dark:border-ink-700 dark:text-ink-400 dark:hover:bg-ink-800 font-semibold text-sm transition font-sans">
                                ${p}
                            </button>
                        `;
                    }
                }
                numbersContainer.innerHTML = numbersHtml;
            }

            const prevBtn = document.getElementById('page-prev-' + gridIndex);
            const nextBtn = document.getElementById('page-next-' + gridIndex);
            if (prevBtn) prevBtn.disabled = page <= 1;
            if (nextBtn) nextBtn.disabled = page >= totalPages;

            // Hide pagination container completely if only 1 page
            const container = document.getElementById('pagination-container-' + gridIndex);
            if (container) {
                if (totalPages <= 1) {
                    container.classList.add('hidden');
                } else {
                    container.classList.remove('hidden');
                }
            }
        }

        function changePage(gridIndex, delta) {
            gridPages[gridIndex] = (gridPages[gridIndex] || 1) + delta;
            renderPage(gridIndex);
        }

        function goToPage(gridIndex, pageNum) {
            gridPages[gridIndex] = pageNum;
            renderPage(gridIndex);
        }

        // Auto select first products on load
        document.addEventListener('DOMContentLoaded', () => {
            const grid0 = document.getElementById('product-grid-0');
            if (grid0) {
                const firstCard = grid0.querySelector('.product-card-container');
                if (firstCard) firstCard.click();
            }

            const grid1 = document.getElementById('product-grid-1');
            if (grid1) {
                const firstCard = grid1.querySelector('.product-card-container');
                if (firstCard) firstCard.click();
            }
        });

        // Export hasil perbandingan yang sedang tampil ke CSV
        const exportData = {
            shop1: { name: @json($shop1_data['shop_name']), products: @json($shop1_data['products']) },
            shop2: { name: @json($shop2_data['shop_name']), products: @json($shop2_data['products']) },
        };

        function exportComparisonCSV() {
            const rows = [['Toko', 'Judul Produk', 'Harga', 'Rating', 'Terjual', 'Link']];
            [exportData.shop1, exportData.shop2].forEach(shop => {
                shop.products.forEach(p => {
                    rows.push([shop.name, p.title, p.price, p.rating ?? '', p.sold ?? '', p.link]);
                });
            });
            const csv = rows.map(r => r.map(field => `"${String(field ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
            const blob = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `perbandingan-${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(link);
            link.click();
            link.remove();
        }

        // Tampilkan indikator loading saat generate PDF (proses scraping ulang di server)
        document.getElementById('export-pdf-form').addEventListener('submit', () => {
            const btn = document.getElementById('export-pdf-btn');
            const icon = document.getElementById('export-pdf-icon');
            const text = document.getElementById('export-pdf-text');
            btn.disabled = true;
            icon.classList.add('animate-spin');
            icon.textContent = 'progress_activity';
            text.textContent = 'Membuat PDF...';
            setTimeout(() => {
                btn.disabled = false;
                icon.classList.remove('animate-spin');
                icon.textContent = 'picture_as_pdf';
                text.textContent = 'Export PDF';
            }, 8000);
        });

        // Tampilkan overlay loading saat pindah halaman
        document.querySelectorAll('.compare-nav-form').forEach(form => {
            form.addEventListener('submit', () => {
                document.getElementById('nav-loading-overlay').classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
