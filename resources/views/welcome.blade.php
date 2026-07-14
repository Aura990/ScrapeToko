<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScrapeToko - Solusi Scraping Tokopedia Terbaik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>
<body class="bg-white text-ink-800 font-sans antialiased">

    <!-- Navbar -->
    <nav class="bg-white sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="text-lg font-extrabold text-ink-900 flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600">
                        <span class="icon icon-sm text-white">shopping_cart</span>
                    </span>
                    ScrapeToko
                </a>
                <div>
                    @guest
                        <a href="{{ route('login') }}" class="btn-primary">
                            <span class="icon icon-sm">login</span>
                            Masuk
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn-secondary">
                            Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-surface relative overflow-hidden">
        <div class="pointer-events-none absolute -top-24 -right-24 h-96 w-96 rounded-full bg-brand-200/30 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-12 left-0 h-72 w-72 rounded-full bg-brand-200/30 blur-3xl"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="badge-brand mb-6">
                        <span class="icon icon-sm text-brand-600">auto_awesome</span>
                        Otomatisasi Riset Kompetitor
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6 tracking-tight text-ink-900">
                        Scraping Toko <span class="text-brand-600">Mudah &amp;</span><br>
                        <span class="text-brand-600">Efisien</span>
                    </h1>
                    <p class="text-lg text-ink-500 mb-10 max-w-md">
                        Otomatisasi pengumpulan data produk dari Tokopedia dan bandingkan toko Anda dengan kompetitor secara instan dan akurat.
                    </p>
                    <a href="{{ route('login') }}" class="btn-primary text-base px-6 py-3">
                        <span class="icon icon-md">add</span>
                        Mulai Sekarang
                    </a>
                </div>
                <div class="flex justify-center lg:justify-end">
                    <img src="{{ asset('images/illustrations/main-hero.png') }}" alt="Ilustrasi ScrapeToko"
                         class="w-full max-w-md lg:max-w-lg h-auto object-contain select-none pointer-events-none">
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-24 bg-surface">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-ink-900 mb-3 tracking-tight">Fitur Unggulan</h2>
                <p class="text-ink-400 max-w-xl mx-auto">Semua yang Anda butuhkan untuk memantau performa toko dan kompetitor dalam satu tempat.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover p-8">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 mb-6">
                        <span class="icon icon-lg">bolt</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-ink-900">Scraping Cepat</h3>
                    <p class="text-ink-400 leading-relaxed">Kumpulkan data produk dari Tokopedia dengan kecepatan tinggi dan minim gangguan.</p>
                </div>
                <div class="card-hover p-8">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 mb-6">
                        <span class="icon icon-lg">bar_chart</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-ink-900">Analisis Data</h3>
                    <p class="text-ink-400 leading-relaxed">Bandingkan produk dan harga antar toko untuk mendapatkan insight yang berharga.</p>
                </div>
                <div class="card-hover p-8">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-50 text-brand-600 mb-6">
                        <span class="icon icon-lg">tune</span>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-ink-900">Kustomisasi</h3>
                    <p class="text-ink-400 leading-relaxed">Sesuaikan kata kunci, urutan, dan halaman pencarian sesuai kebutuhan Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-ink-950 text-ink-300 py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-between gap-10">
                <div class="max-w-xs">
                    <h3 class="text-xl font-bold text-white mb-3 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600">
                            <span class="icon icon-sm text-white">shopping_cart</span>
                        </span>
                        ScrapeToko
                    </h3>
                    <p class="text-ink-400 text-sm leading-relaxed">Solusi scraping dan komparasi harga terbaik untuk toko Tokopedia Anda.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-white transition duration-150">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition duration-150">Layanan</a></li>
                        <li><a href="#" class="hover:text-white transition duration-150">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Hubungi Kami</h4>
                    <p class="text-sm mb-2">Email: info@scrapetoko.com</p>
                    <p class="text-sm">Telepon: (021) 1234-5678</p>
                </div>
            </div>
            <hr class="border-white/10 my-8">
            <p class="text-center text-sm text-ink-500">&copy; {{ date('Y') }} ScrapeToko. Hak cipta dilindungi undang-undang.</p>
        </div>
    </footer>
</body>
</html>
