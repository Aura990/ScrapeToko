<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-page-header
                title="Manajemen Pengguna"
                subtitle="Kelola akun karyawan dan admin."
                illustration="manajemen-pengguna.png"
            />
            <div class="card overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <a href="{{ route('users.create') }}" class="btn-primary">
                            <span class="icon icon-sm">add</span>
                            Tambah Pengguna
                        </a>
                        <form method="GET" action="{{ route('users.index') }}" class="flex w-full md:w-auto">
                            <input type="text" name="search" placeholder="Cari pengguna..." class="field-input rounded-r-none w-full md:w-64" value="{{ request('search') }}">
                            <button type="submit" class="btn-primary rounded-l-none">
                                <span class="icon icon-sm">search</span>
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-ink-100 dark:border-ink-800">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Peran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                @forelse ($users as $user)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <span class="avatar-brand h-9 w-9 text-xs">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                                <span class="font-medium text-ink-900 dark:text-white">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td id="status-{{ $user->id }}">
                                            <span class="badge-neutral">Memuat...</span>
                                        </td>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('users.edit', $user) }}" class="text-ink-500 hover:text-brand-600 font-medium">Edit</a>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-coral-600 hover:text-coral-700 font-medium" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-ink-400 py-8">Tidak ada pengguna ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateUserStatus(userId, isOnline) {
            const statusElement = document.getElementById(`status-${userId}`);
            if (statusElement) {
                statusElement.innerHTML = isOnline
                    ? '<span class="badge-success"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Online</span>'
                    : '<span class="badge-neutral">Offline</span>';
            }
        }

        function fetchUserStatuses() {
            fetch('{{ route("users.statuses") }}')
                .then(response => response.json())
                .then(data => {
                    data.forEach(user => {
                        updateUserStatus(user.id, user.is_online);
                    });
                });
        }

        // Initial fetch
        fetchUserStatuses();

        // Set up polling
        setInterval(fetchUserStatuses, 5000);
    </script>
</x-app-layout>
