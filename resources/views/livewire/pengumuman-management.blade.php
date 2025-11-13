<div class="p-6 min-h-screen">
    <div class="flex justify-between items-center mb-6 p-6 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pengumuman Management</h2>
        @can('pengumuman.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Tambah Pengumuman
            </button>
        @endcan
    </div>

    @if (session()->has('message'))
        <div
            class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-gray-100 dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Lampiran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $item->judul }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white">
                            <span title="{{ $item->deskripsi }}">{{ Str::limit($item->deskripsi, 80) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($item->file)
                                <a class="text-blue-600 dark:text-blue-400 underline" href="{{ asset('storage/'.$item->file) }}" target="_blank">Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->aktif)
                                <span class="inline-flex px-2 py-1 text-xs bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded-full">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                            {{ $item->publish_at ? $item->publish_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('pengumuman.edit')
                                <button wire:click="edit({{ $item->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('pengumuman.delete')
                                <button wire:click="delete({{ $item->id }})" onclick="return confirm('Hapus pengumuman ini?')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                    Delete
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada pengumuman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-gray-900 dark:text-white">
        {{ $items->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $isEditing ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}
                    </h3>

                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                            <input type="text" wire:model="judul"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('judul') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea wire:model="deskripsi" rows="5"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                            @error('deskripsi') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="aktif" class="rounded border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                            @error('aktif') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Publish Date (opsional)</label>
                            <input type="datetime-local" wire:model="publish_at"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('publish_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lampiran (opsional)</label>
                            <input type="file" wire:model="file"
                                class="mt-1 block w-full text-sm text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-gray-700 file:text-blue-700 dark:file:text-white hover:file:bg-blue-100 dark:hover:file:bg-gray-600">
                            @error('file') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @if ($file)
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">File terpilih: {{ $file->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Batal
                            </button>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
