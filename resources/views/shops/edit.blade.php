<x-app-layout>
    <div class="py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="page-title">Edit Toko</h1>
                <p class="page-subtitle">Perbarui informasi toko.</p>
            </div>
            <div class="card">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('shops.update', $shop) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="link" :value="__('Link Toko')" />
                            <x-text-input id="link" class="block mt-1 w-full" type="url" name="link" :value="old('link', $shop->link)" required autofocus />
                            <x-input-error :messages="$errors->get('link')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="jenis" :value="__('Jenis Toko')" />
                            <select id="jenis" name="jenis" class="field-select" required>
                                <option value="dikelola" {{ old('jenis', $shop->jenis) == 'dikelola' ? 'selected' : '' }}>Dikelola</option>
                                <option value="saingan" {{ old('jenis', $shop->jenis) == 'saingan' ? 'selected' : '' }}>Saingan</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('shops.index') }}" class="btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn-primary">
                                {{ __('Perbarui') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
