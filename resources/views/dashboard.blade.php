<x-app-layout>
    @php
        $shopCount = App\Models\Shop::count();
        $managedCount = App\Models\Shop::where('jenis', 'dikelola')->count();
        $competitorCount = App\Models\Shop::where('jenis', 'saingan')->count();
        $twoMinutesAgo = Carbon\Carbon::now()->subMinutes(2);
        $onlineEmployees = App\Models\User::where('last_seen', '>=', $twoMinutesAgo)->get();
        $onlineEmployeesCount = $onlineEmployees->count();
        $comparisonCount = App\Models\ComparisonHistory::count();
        $recentComparisons = App\Models\ComparisonHistory::with(['shop1', 'shop2', 'user'])->latest()->limit(5)->get();
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Hero -->
            <x-page-header
                title="Dashboard ScrapeToko"
                subtitle="Ringkasan aktivitas toko dan tim anda."
                illustration="dashboard.png"
            />

            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="card-hover">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-ink-400 uppercase tracking-wider mb-3">Jumlah Toko</p>
                            <p class="text-4xl font-extrabold text-ink-900 dark:text-white">{{ number_format($shopCount) }}</p>
                        </div>
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-coral-50 text-coral-500 dark:bg-coral-900/30 dark:text-coral-300">
                            <span class="icon icon-lg">storefront</span>
                        </div>
                    </div>
                </div>
                <div class="card-hover">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-ink-400 uppercase tracking-wider mb-3">Karyawan Online</p>
                            <p id="online-employees-count" class="text-4xl font-extrabold text-ink-900 dark:text-white">{{ $onlineEmployeesCount }}</p>
                        </div>
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-mint-50 text-mint-500 dark:bg-mint-900/30 dark:text-mint-300">
                            <span class="icon icon-lg">group</span>
                        </div>
                    </div>
                </div>
                <div class="card-hover">
                    <div class="p-6 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-ink-400 uppercase tracking-wider mb-3">Total Perbandingan</p>
                            <p class="text-4xl font-extrabold text-ink-900 dark:text-white">{{ number_format($comparisonCount) }}</p>
                        </div>
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                            <span class="icon icon-lg">compare_arrows</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-6 mb-8">
                <!-- Grafik distribusi toko -->
                <div class="card p-6 lg:col-span-1">
                    <h2 class="text-lg font-bold text-ink-900 dark:text-white mb-4">Distribusi Toko</h2>
                    @if ($shopCount > 0)
                        <canvas id="shopDistributionChart" height="220"></canvas>
                        <div class="flex justify-center gap-6 mt-4 text-sm">
                            <span class="flex items-center gap-2 text-ink-500 dark:text-ink-400">
                                <span class="h-2.5 w-2.5 rounded-full bg-mint-500"></span> Dikelola ({{ $managedCount }})
                            </span>
                            <span class="flex items-center gap-2 text-ink-500 dark:text-ink-400">
                                <span class="h-2.5 w-2.5 rounded-full bg-coral-500"></span> Saingan ({{ $competitorCount }})
                            </span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-center py-10 text-ink-400">
                            <span class="icon icon-lg mb-2">storefront</span>
                            <p class="text-sm">Belum ada toko yang ditambahkan.</p>
                        </div>
                    @endif
                </div>

                <!-- Aktivitas perbandingan terbaru -->
                <div class="card overflow-hidden lg:col-span-2">
                    <div class="px-6 py-5 flex items-center justify-between gap-3 border-b border-ink-100 dark:border-ink-800">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-coral-50 text-coral-500 dark:bg-coral-900/30 dark:text-coral-300">
                                <span class="icon icon-md">history</span>
                            </span>
                            <h2 class="text-lg font-bold text-ink-900 dark:text-white">Aktivitas Perbandingan Terbaru</h2>
                        </div>
                        <a href="{{ route('comparison.history') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 shrink-0">
                            Lihat semua
                        </a>
                    </div>
                    <div class="divide-y divide-ink-100 dark:divide-ink-800">
                        @forelse ($recentComparisons as $history)
                            <div class="px-6 py-4 flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="font-semibold text-ink-900 dark:text-white truncate">"{{ $history->keyword }}"</p>
                                    <p class="text-sm text-ink-400 truncate">
                                        {{ $history->shop1?->name ?? 'Toko dihapus' }} vs {{ $history->shop2?->name ?? 'Toko dihapus' }}
                                        &middot; {{ $history->user?->name ?? 'Sistem' }}
                                    </p>
                                </div>
                                <span class="text-xs text-ink-400 shrink-0">{{ $history->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center text-center py-10 text-ink-400">
                                <span class="icon icon-lg mb-2">compare_arrows</span>
                                <p class="text-sm">Belum ada perbandingan yang dilakukan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="px-6 py-5 flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                        <span class="icon icon-md">group</span>
                    </span>
                    <h2 class="text-lg font-bold text-ink-900 dark:text-white">Karyawan Online</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="users-table-body">
                            @forelse($onlineEmployees as $employee)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <span class="avatar-coral h-9 w-9 text-xs">
                                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                                            </span>
                                            <span class="font-medium text-ink-900 dark:text-white">{{ $employee->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $employee->email }}</td>
                                    <td>{{ ucfirst($employee->role) }}</td>
                                    <td>
                                        <span class="badge-success">
                                            <span class="h-1.5 w-1.5 rounded-full bg-mint-500"></span>
                                            Online
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-ink-400 py-8">Tidak ada karyawan yang sedang online.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($shopCount > 0)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
        <script>
            const ctx = document.getElementById('shopDistributionChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dikelola', 'Saingan'],
                        datasets: [{
                            data: [{{ $managedCount }}, {{ $competitorCount }}],
                            backgroundColor: ['#22a877', '#f43f47'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '70%',
                        plugins: { legend: { display: false } }
                    }
                });
            }
        </script>
    @endif

    <script>
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function fetchUserStatuses() {
            fetch('{{ route("users.statuses") }}')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('users-table-body');
                    tableBody.innerHTML = '';
                    let onlineCount = 0;
                    data.forEach(user => {
                        if (user.is_online) {
                            onlineCount++;
                            const row = document.createElement('tr');
                            row.id = `user-${user.id}`;

                            const userRole = capitalizeFirstLetter(user.role);
                            const initials = user.name.substring(0, 2).toUpperCase();

                            row.innerHTML = `
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="avatar-coral h-9 w-9 text-xs">${initials}</span>
                                        <span class="font-medium text-ink-900 dark:text-white">${user.name}</span>
                                    </div>
                                </td>
                                <td>${user.email}</td>
                                <td>${userRole}</td>
                                <td>
                                    <span class="badge-success">
                                        <span class="h-1.5 w-1.5 rounded-full bg-mint-500"></span>
                                        Online
                                    </span>
                                </td>
                            `;

                            tableBody.appendChild(row);
                        }
                    });
                    if (onlineCount === 0) {
                        tableBody.innerHTML = '<tr><td colspan="4" class="text-center text-ink-400 py-8">Tidak ada karyawan yang sedang online.</td></tr>';
                    }
                    document.getElementById('online-employees-count').textContent = onlineCount;
                });
        }

        setInterval(fetchUserStatuses, 5000); // Polling setiap 5 detik
    </script>
</x-app-layout>
