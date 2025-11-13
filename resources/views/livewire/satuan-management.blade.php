{{-- resources/views/livewire/satuan-management.blade.php --}}

<div class="p-6 min-h-screen">
    <div class="flex justify-between items-center mb-6 p-5 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Satuan Management</h2>
        @can('satuan.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Add Satuan
            </button>
        @endcan
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-gray-100 dark:bg-gray-800 rounded shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Parent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($items as $row)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->kode }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $row->parent->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($row->aktif)
                                <span class="inline-flex px-2 py-1 text-xs bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded-full">Active</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('satuan.edit')
                                <button wire:click="edit({{ $row->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('satuan.delete')
                                <button wire:click="delete({{ $row->id }})" onclick="return confirm('Are you sure?')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                    Delete
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-gray-900 dark:text-white">
        {{ $items->links() }}
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $isEditing ? 'Edit Satuan' : 'Add Satuan' }}
                    </h3>

                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <input type="text" wire:model="nama"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('nama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode</label>
                            <input type="text" wire:model="kode"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('kode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Parent</label>
                            <select wire:model="parent_id"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">-- None --</option>
                                @foreach($parentOptions as $opt)
                                    <option value="{{ $opt->id }}" @if($isEditing && $opt->id === $satuanId) disabled @endif>
                                        {{ $opt->nama }} ({{ $opt->kode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="aktif" class="rounded border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                            </label>
                            @error('aktif') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="closeModal"
                                class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
