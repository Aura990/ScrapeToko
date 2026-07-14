<aside
    x-data="{
        userMenuOpen: false,
        notifOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        unreadCount: 0,
        notifications: [],
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
        toggleLayout() {
            document.documentElement.classList.remove('nav-sidebar');
            localStorage.setItem('navLayout', 'top');
        },
        fetchNotifications() {
            fetch('{{ route('notifications.unread') }}')
                .then(res => res.json())
                .then(data => {
                    this.unreadCount = data.unread_count;
                    this.notifications = data.notifications;
                })
                .catch(() => {});
        },
        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000);
        }
    }"
    class="sidebar-nav fixed inset-y-0 left-0 z-40 w-20 flex-col items-center py-6 bg-white border-r border-ink-100 dark:bg-ink-900 dark:border-ink-800"
>
    <!-- Logo -->
    <a href="{{ route('dashboard') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 shrink-0 mb-8">
        <span class="icon icon-md text-white">shopping_cart</span>
    </a>

    <!-- Nav Links -->
    <nav class="flex-1 flex flex-col items-center gap-2 w-full px-3">
        <div class="group relative w-full">
            <a href="{{ route('dashboard') }}"
               class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl transition
               {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100' }}">
                <span class="icon icon-md">home</span>
            </a>
            <span class="sidebar-tooltip">Dashboard</span>
        </div>

        @role('admin')
            <div class="group relative w-full">
                <a href="{{ route('users.index') }}"
                   class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl transition
                   {{ request()->routeIs('users.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100' }}">
                    <span class="icon icon-md">group</span>
                </a>
                <span class="sidebar-tooltip">Manajemen Pengguna</span>
            </div>
            <div class="group relative w-full">
                <a href="{{ route('shops.index') }}"
                   class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl transition
                   {{ request()->routeIs('shops.*') ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100' }}">
                    <span class="icon icon-md">storefront</span>
                </a>
                <span class="sidebar-tooltip">Manajemen Toko</span>
            </div>
        @endrole

        <div class="group relative w-full">
            <a href="{{ route('comparison.index') }}"
               class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl transition
               {{ request()->routeIs('comparison.index') || request()->routeIs('comparison.compare') ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100' }}">
                <span class="icon icon-md">compare_arrows</span>
            </a>
            <span class="sidebar-tooltip">Komparasi Toko</span>
        </div>

        <div class="group relative w-full">
            <a href="{{ route('comparison.history') }}"
               class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl transition
               {{ request()->routeIs('comparison.history') ? 'bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300' : 'text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100' }}">
                <span class="icon icon-md">history</span>
            </a>
            <span class="sidebar-tooltip">Riwayat Perbandingan</span>
        </div>
    </nav>

    <!-- Bottom actions -->
    <div class="flex flex-col items-center gap-2 w-full px-3 pt-3 border-t border-ink-100 dark:border-ink-800">
        <div class="group relative w-full">
            <button @click="notifOpen = !notifOpen; if (notifOpen) fetchNotifications()"
                    class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100 transition relative">
                <span class="icon icon-md">notifications</span>
                <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                      class="absolute top-1 right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-coral-500 px-1 text-[10px] font-bold text-white"></span>
            </button>
            <span class="sidebar-tooltip">Notifikasi</span>

            <div x-show="notifOpen" x-transition x-cloak @click.outside="notifOpen = false"
                 class="absolute left-full bottom-0 ml-3 w-80 rounded-xl shadow-card bg-white border border-ink-100 dark:bg-ink-800 dark:border-ink-700 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-ink-100 dark:border-ink-700 flex items-center justify-between">
                    <span class="font-semibold text-sm text-ink-900 dark:text-white">Notifikasi</span>
                    <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-brand-600 dark:text-brand-400">Lihat semua</a>
                </div>
                <template x-if="notifications.length === 0">
                    <p class="text-center text-sm text-ink-400 py-6">Belum ada notifikasi.</p>
                </template>
                <template x-for="notif in notifications" :key="notif.id">
                    <div class="px-4 py-3 border-b border-ink-50 dark:border-ink-700/50 last:border-0" :class="!notif.read ? 'bg-brand-50/50 dark:bg-brand-900/10' : ''">
                        <p class="text-xs text-ink-700 dark:text-ink-200" x-text="notif.message"></p>
                        <p class="text-[11px] text-ink-400 mt-1" x-text="notif.created_at"></p>
                    </div>
                </template>
            </div>
        </div>

        <div class="group relative w-full">
            <button @click="toggleDark()" class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100 transition">
                <span class="icon icon-md" x-text="dark ? 'light_mode' : 'dark_mode'">dark_mode</span>
            </button>
            <span class="sidebar-tooltip" x-text="dark ? 'Mode terang' : 'Mode gelap'">Mode gelap</span>
        </div>

        <div class="group relative w-full">
            <button @click="toggleLayout()" class="flex h-11 w-11 mx-auto items-center justify-center rounded-xl text-ink-400 hover:bg-ink-50 hover:text-ink-700 dark:hover:bg-ink-800 dark:hover:text-ink-100 transition">
                <span class="icon icon-md">dock_to_right</span>
            </button>
            <span class="sidebar-tooltip">Tampilkan sebagai navbar</span>
        </div>

        <div class="group relative w-full">
            <button @click="userMenuOpen = !userMenuOpen" class="flex h-11 w-11 mx-auto items-center justify-center rounded-full avatar-brand text-sm">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </button>
            <span class="sidebar-tooltip" x-show="!userMenuOpen">{{ Auth::user()->name }}</span>

            <div x-show="userMenuOpen" x-transition x-cloak @click.outside="userMenuOpen = false"
                 class="absolute left-full bottom-0 ml-3 w-48 rounded-xl shadow-card bg-white border border-ink-100 dark:bg-ink-800 dark:border-ink-700 overflow-hidden z-50 py-1">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-ink-600 hover:bg-brand-50 hover:text-brand-700 dark:text-ink-300 dark:hover:bg-ink-700 dark:hover:text-brand-300 transition">
                    {{ __('Profil') }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                       class="block px-4 py-2.5 text-sm text-ink-600 hover:bg-brand-50 hover:text-brand-700 dark:text-ink-300 dark:hover:bg-ink-700 dark:hover:text-brand-300 transition">
                        {{ __('Keluar') }}
                    </a>
                </form>
            </div>
        </div>
    </div>
</aside>
