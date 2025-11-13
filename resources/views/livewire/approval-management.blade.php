{{-- resources/views/livewire/approval-management.blade.php --}}

<div class="p-6 min-h-screen">
    <div class="flex justify-between items-center mb-6 p-5 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Approval Management</h2>
        @can('approval.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Add Approval
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proposal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelaku</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($approvals as $a)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $a->proposal->kode_usulan ?? '-' }} - {{ $a->proposal->judul ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span @class([
                                'inline-flex px-2 py-1 text-xs rounded-full',
                                'bg-blue-500 text-blue-800' => $a->aksi === 'diajukan',
                                'bg-yellow-500 text-yellow-800' => $a->aksi === 'diverifikasi',
                                'bg-green-500 text-green-800' => $a->aksi === 'disetujui',
                                'bg-red-500 text-red-800' => $a->aksi === 'ditolak',
                                'bg-gray-500 text-gray-800' => $a->aksi === 'revisi',
                            ])>
                                {{ ucfirst($a->aksi) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $a->actor->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ optional($a->acted_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4">{{ \Illuminate\Support\Str::limit($a->catatan, 80) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('approval.edit')
                                <button type="button" wire:click="edit({{ $a->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('approval.delete')
                                <button type="button" wire:click="delete({{ $a->id }})" onclick="return confirm('Are you sure?')"
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

    <div class="mt-4">
        {{ $approvals->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $isEditing ? 'Edit' : 'Create' }} Approval</h3>
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Proposal</label>
                                <select wire:model.defer="proposal_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700">
                                    <option value="">-- Pilih --</option>
                                    @foreach($proposalOptions as $p)
                                        <option value="{{ $p->id }}">{{ $p->kode_usulan }} - {{ $p->judul }}</option>
                                    @endforeach
                                </select>
                                @error('proposal_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Aksi</label>
                                <select wire:model.defer="aksi" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700">
                                    @foreach($aksiOptions as $st)
                                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                @error('aksi') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Waktu Aksi</label>
                                <input type="datetime-local" wire:model.defer="acted_at" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700" />
                                @error('acted_at') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            {{-- Actor default ke user saat ini (diisi otomatis oleh model) --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Catatan</label>
                                <textarea rows="3" wire:model.defer="catatan" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700"></textarea>
                                @error('catatan') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="md:col-span-2 flex justify-end space-x-2 mt-4">
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
