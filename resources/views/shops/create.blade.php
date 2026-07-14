<x-app-layout>
    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header
                title="Tambah Toko"
                subtitle="Daftarkan toko baru untuk dipantau atau dibandingkan."
                illustration="tambah-toko.png"
            />
            <div class="card">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('shops.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="link" :value="__('Link Toko')" />
                            <x-text-input id="link" class="block mt-1 w-full" type="url" name="link" :value="old('link')" required autofocus placeholder="https://www.tokopedia.com/nama-toko" />
                            <x-input-error :messages="$errors->get('link')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="jenis" :value="__('Jenis Toko')" />
                            <select id="jenis" name="jenis" class="field-select" required>
                                <option value="dikelola" {{ old('jenis') == 'dikelola' ? 'selected' : '' }}>Dikelola</option>
                                <option value="saingan" {{ old('jenis') == 'saingan' ? 'selected' : '' }}>Saingan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('shops.index') }}" class="btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn-primary">
                                {{ __('Tambah') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
