<x-guest-layout>
    <div class="w-full">
        <h2 class="text-2xl md:text-3xl font-extrabold text-center text-ink-900 dark:text-white tracking-tight mb-2">
            Masuk ke Akun Anda
        </h2>
        <p class="text-center text-sm md:text-base text-ink-500 dark:text-ink-400 mb-8">Kelola dan bandingkan toko Tokopedia Anda</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form class="space-y-5" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <div class="relative">
                    <span class="icon icon-sm text-ink-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">mail</span>
                    <x-text-input id="email" name="email" type="email" autocomplete="email" required
                                  class="block w-full pl-10" placeholder="nama@example.com" :value="old('email')" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Kata Sandi')" />
                <div class="relative">
                    <span class="icon icon-sm text-ink-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">lock</span>
                    <x-text-input id="password" name="password" type="password" autocomplete="current-password" required
                                  class="block w-full pl-10 pr-10" placeholder="Masukkan kata sandi" />
                    <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-400 hover:text-ink-600 dark:hover:text-ink-200">
                        <span class="icon icon-sm" id="password-toggle-icon">visibility</span>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center gap-2">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="rounded border-ink-300 text-brand-600 shadow-soft focus:ring-brand-500">
                    <span class="text-sm text-ink-600 dark:text-ink-300">Ingat saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full">
                Masuk
            </button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const icon = document.getElementById('password-toggle-icon');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.textContent = isPassword ? 'visibility_off' : 'visibility';
        }
    </script>
</x-guest-layout>
