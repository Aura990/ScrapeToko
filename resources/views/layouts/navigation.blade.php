<nav
    x-data="{
        open: false,
        notifOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        sidebarLayout: document.documentElement.classList.contains('nav-sidebar'),
        unreadCount: 0,
        notifications: [],
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        },
        toggleLayout() {
            this.sidebarLayout = !this.sidebarLayout;
            document.documentElement.classList.toggle('nav-sidebar', this.sidebarLayout);
            localStorage.setItem('navLayout', this.sidebarLayout ? 'sidebar' : 'top');
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
    class="topbar-nav bg-white sticky top-4 z-40 mx-4 mt-4 rounded-2xl shadow-card border border-ink-100 sm:mx-6 lg:mx-8 dark:bg-ink-900 dark:border-ink-800"
>
    <!-- Primary Navigation Menu -->
    <div class="px-4 sm:px-6">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-2 sm:gap-6">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="text-ink-900 dark:text-white font-extrabold text-lg flex items-center gap-2.5 shrink-0">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600">
                        <span class="icon icon-md text-white">shopping_cart</span>
                    </span>
                    <span class="tracking-tight hidden sm:inline">ScrapeToko</span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden md:flex md:items-center md:gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    @role('admin')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" icon="group">
                            {{ __('Pengguna') }}
                        </x-nav-link>
                        <x-nav-link :href="route('shops.index')" :active="request()->routeIs('shops.*')" icon="storefront">
                            {{ __('Toko') }}
                        </x-nav-link>
                    @endrole
                    <x-nav-link :href="route('comparison.index')" :active="request()->routeIs('comparison.index') || request()->routeIs('comparison.compare')" icon="compare_arrows">
                        {{ __('Komparasi') }}
                    </x-nav-link>
                    <x-nav-link :href="route('comparison.history')" :active="request()->routeIs('comparison.history')" icon="history">
                        {{ __('Riwayat') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden md:flex md:items-center gap-1">
                <div class="relative" @click.outside="notifOpen = false">
                    <button @click="notifOpen = !notifOpen; if (notifOpen) fetchNotifications()" type="button" class="theme-toggle relative">
                        <span class="icon icon-md">notifications</span>
                        <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                              class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-coral-500 px-1 text-[10px] font-bold text-white"></span>
                    </button>
                    <div x-show="notifOpen" x-transition x-cloak
                         class="absolute right-0 mt-2 w-80 rounded-xl shadow-card bg-white border border-ink-100 dark:bg-ink-800 dark:border-ink-700 overflow-hidden z-50">
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
                <button @click="toggleLayout()" type="button" class="theme-toggle" title="Tampilkan sebagai sidebar">
                    <span class="icon icon-md">dock_to_left</span>
                </button>
                <button @click="toggleDark()" type="button" class="theme-toggle" :title="dark ? 'Mode terang' : 'Mode gelap'">
                    <span class="icon icon-md" x-text="dark ? 'light_mode' : 'dark_mode'">dark_mode</span>
                </button>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 rounded-full pl-1 pr-2 py-1 text-sm font-semibold text-ink-700 hover:bg-ink-50 dark:text-ink-200 dark:hover:bg-ink-800 transition duration-150 ease-in-out">
                            <span class="avatar-brand h-8 w-8 text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="icon icon-sm text-ink-400">expand_more</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-1 flex items-center gap-1 md:hidden">
                <a href="{{ route('notifications.index') }}" class="theme-toggle relative">
                    <span class="icon icon-md">notifications</span>
                    <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount"
                          class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-coral-500 px-1 text-[10px] font-bold text-white"></span>
                </a>
                <button @click="toggleDark()" type="button" class="theme-toggle">
                    <span class="icon icon-md" x-text="dark ? 'light_mode' : 'dark_mode'">dark_mode</span>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-ink-500 hover:text-ink-900 hover:bg-ink-50 dark:text-ink-300 dark:hover:text-white dark:hover:bg-ink-800 focus:outline-none transition duration-150 ease-in-out">
                    <span class="icon icon-lg" x-text="open ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-ink-100 dark:border-ink-800">
        <div class="px-3 pt-3 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @role('admin')
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" icon="group">
                    {{ __('Manajemen Pengguna') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('shops.index')" :active="request()->routeIs('shops.*')" icon="storefront">
                    {{ __('Manajemen Toko') }}
                </x-responsive-nav-link>
            @endrole
            <x-responsive-nav-link :href="route('comparison.index')" :active="request()->routeIs('comparison.index') || request()->routeIs('comparison.compare')" icon="compare_arrows">
                {{ __('Komparasi Toko') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('comparison.history')" :active="request()->routeIs('comparison.history')" icon="history">
                {{ __('Riwayat Perbandingan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')" icon="notifications">
                {{ __('Notifikasi') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-ink-100 dark:border-ink-800">
            <div class="px-4 flex items-center gap-3">
                <span class="avatar-brand h-9 w-9">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
                <div>
                    <div class="font-semibold text-base text-ink-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-ink-400">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 px-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" icon="person">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" icon="logout"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
