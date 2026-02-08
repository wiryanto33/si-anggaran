{{-- resources/views/livewire/dashboard.blade.php --}}

<div class="p-6  min-h-screen">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if (isset($stats['users']))
            <div class="bg-gray-100 dark:bg-neutral-900 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Users
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['users'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($stats['proposals_total']))
            <div class="bg-gray-100 dark:bg-neutral-900 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M4 3a1 1 0 00-1 1v12a1 1 0 001.447.894L10 14.118l5.553 2.776A1 1 0 0017 16V4a1 1 0 00-1-1H4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total
                                    Proposals</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['proposals_total'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($stats['proposals_diajukan']))
            <div class="bg-gray-100 dark:bg-neutral-900 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center"></div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Diajukan</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['proposals_diajukan'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($stats['proposals_disetujui']))
            <div class="bg-gray-100 dark:bg-neutral-900 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center"></div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Disetujui</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['proposals_disetujui'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($stats['proposals_ditolak']))
            <div class="bg-gray-100 dark:bg-neutral-900 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center"></div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Ditolak</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['proposals_ditolak'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-gray-100 dark:bg-neutral-900 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @can('user.create')
                    <a href="{{ route('users.index') }}"
                        class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded text-center block">
                        Manage Users
                    </a>
                @endcan

                @can('proposal.view')
                    <a href="{{ route('proposals.index') }}"
                        class="bg-indigo-500 hover:bg-indigo-600 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white px-4 py-2 rounded text-center block">
                        Manage Proposals
                    </a>
                @endcan

                @can('role.view')
                    <a href="{{ route('roles.index') }}"
                        class="bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-700 dark:hover:bg-yellow-800 text-white px-4 py-2 rounded text-center block">
                        Manage Roles
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="mt-8 bg-gray-100 dark:bg-neutral-900 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Your Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Roles</dt>
                    <dd class="mt-1">
                        @foreach (auth()->user()->roles as $role)
                            <span
                                class="inline-flex px-2 py-1 text-xs bg-blue-100 dark:bg-blue-700 text-blue-800 dark:text-white rounded-full mr-2">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Permissions</dt>
                    <dd class="mt-1">
                        <div class="flex flex-wrap gap-1">
                            @foreach (auth()->user()->getAllPermissions()->take(10) as $permission)
                                <span
                                    class="inline-flex px-2 py-1 text-xs bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded-full">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                            @if (auth()->user()->getAllPermissions()->count() > 10)
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    +{{ auth()->user()->getAllPermissions()->count() - 10 }} more
                                </span>
                            @endif
                        </div>
                    </dd>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 bg-gray-100 dark:bg-neutral-900 shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">Pengumuman</h3>
            @if (isset($pengumuman) && $pengumuman->count())
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($pengumuman as $p)
                        <li class="py-3 flex items-start justify-between">
                            <div>
                                <button type="button" wire:click="openPengumuman({{ $p->id }})"
                                    class="font-medium text-left text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $p->judul }}
                                </button>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ Str::limit($p->deskripsi, 120) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    Dipublikasikan:
                                    {{ $p->publish_at ? $p->publish_at->format('d M Y H:i') : $p->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <div class="ml-4">
                                @if ($p->file)
                                    <a href="{{ asset('storage/' . $p->file) }}" target="_blank"
                                        class="text-blue-600 dark:text-blue-400 underline">Lampiran</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                {{-- @can('pengumuman.view')
                    <div class="mt-4">
                        <a href="{{ route('pengumuman.index') }}" wire:navigate class="text-sm text-blue-600 dark:text-blue-400 underline">Kelola Pengumuman</a>
                    </div>
                @endcan --}}
            @else
                <p class="text-gray-600 dark:text-gray-400">Belum ada pengumuman.</p>
            @endif
        </div>
    </div>

    {{-- resources/views/livewire/dashboard.blade.php --}}

    @if ($showPengumumanModal && $selectedPengumuman)
        <div
            class="fixed inset-0 z-50 overflow-y-auto backdrop-blur-sm bg-black/40 flex items-center justify-center p-4">
            <div
                class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-200 dark:border-gray-700">

                <div
                    class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-t-xl">
                    <div>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $selectedPengumuman['judul'] }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Dipublikasikan:
                            {{ \Carbon\Carbon::parse($selectedPengumuman['publish_at'] ?? $selectedPengumuman['created_at'])->format('d M Y H:i') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closePengumuman"
                        class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                        {!! nl2br(e($selectedPengumuman['deskripsi'])) !!}
                    </div>

                    @if (!empty($selectedPengumuman['file']))
                        <div class="mt-4 border-t pt-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Dokumen
                                Lampiran:</label>
                            <div class="w-full bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden"
                                style="height: 60vh;">
                                <embed src="{{ asset('storage/' . $selectedPengumuman['file']) }}"
                                    type="application/pdf" width="100%" height="100%" />
                            </div>
                        </div>
                    @endif
                </div>

                <div
                    class="flex items-center justify-end p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl gap-3">
                    @if (!empty($selectedPengumuman['file']))
                        <a href="{{ asset('storage/' . $selectedPengumuman['file']) }}" download
                            class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                    <button type="button" wire:click="closePengumuman"
                        class="px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
