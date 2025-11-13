{{-- resources/views/livewire/annual-budget-management.blade.php --}}

<div class="p-6 min-h-screen">
    <div class="flex justify-between items-center mb-6 p-5 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Annual Budget Management</h2>
        @can('annualbudget.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Add Budget
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Dokumen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($budgets as $b)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $b->tahun }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $b->satuan->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $b->nomor_dokumen ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span @class([
                                'inline-flex px-2 py-1 text-xs rounded-full',
                                'bg-gray-500 text-gray-800' => $b->status === 'draft',
                                'bg-green-500 text-green-800' => $b->status === 'final',
                            ])>
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($b->total_rencana, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('annualbudget.view')
                                <button type="button" wire:click="viewBudget({{ $b->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">
                                    View
                                </button>
                            @endcan
                            @can('annualbudget.edit')
                                <button type="button" wire:click="edit({{ $b->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('annualbudget.delete')
                                <button type="button" wire:click="delete({{ $b->id }})" onclick="return confirm('Are you sure?')"
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
        {{ $budgets->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $budgetId ? 'Edit' : 'Create' }} Budget</h3>
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tahun</label>
                                <input type="number" wire:model.defer="tahun" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700" />
                                @error('tahun') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Satuan</label>
                                <select wire:model.defer="satuan_id" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700">
                                    <option value="">-- Pilih --</option>
                                    @foreach($satuanOptions as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                    @endforeach
                                </select>
                                @error('satuan_id') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nomor Dokumen</label>
                                <input type="text" wire:model.defer="nomor_dokumen" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700" />
                                @error('nomor_dokumen') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Status</label>
                                <select wire:model.defer="status" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700">
                                    @foreach($statusOptions as $st)
                                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Total Rencana</label>
                                <input type="number" step="0.01" wire:model="total_rencana" class="mt-1 w-full rounded border-gray-300 dark:bg-gray-700" disabled />
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white">Items</h4>
                                <button type="button" wire:click="addItemRow" class="px-3 py-1 rounded bg-green-600 text-white">Tambah</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-700">
                                            <th class="px-2 py-2 text-left">Uraian</th>
                                            <th class="px-2 py-2 text-right">Qty</th>
                                            <th class="px-2 py-2 text-right">Harga Satuan</th>
                                            <th class="px-2 py-2 text-right">Subtotal</th>
                                            <th class="px-2 py-2 text-left">Sumber Usulan</th>
                                            <th class="px-2 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $i => $row)
                                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                                <td class="px-2 py-2"><input type="text" wire:model.lazy="items.{{ $i }}.uraian" class="w-full rounded border-gray-300 dark:bg-gray-700" /></td>
                                                <td class="px-2 py-2"><input type="number" step="0.01" wire:model.lazy="items.{{ $i }}.qty" class="w-full text-right rounded border-gray-300 dark:bg-gray-700" /></td>
                                                <td class="px-2 py-2"><input type="number" step="0.01" wire:model.lazy="items.{{ $i }}.harga_satuan" class="w-full text-right rounded border-gray-300 dark:bg-gray-700" /></td>
                                                <td class="px-2 py-2 text-right">Rp {{ number_format((float)($items[$i]['subtotal'] ?? 0), 2, ',', '.') }}</td>
                                                <td class="px-2 py-2">
                                                    <select wire:model.lazy="items.{{ $i }}.sumber_proposal_id" class="w-full rounded border-gray-300 dark:bg-gray-700">
                                                        <option value="">-</option>
                                                        @foreach($proposalOptions as $p)
                                                            <option value="{{ $p->id }}">{{ $p->kode_usulan }} - {{ $p->judul }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="px-2 py-2 text-right">
                                                    <button type="button" wire:click="removeItemRow({{ $i }})" class="px-2 py-1 text-white bg-red-600 rounded">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

    @if($showViewModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Detail Budget</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">Tahun</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['tahun'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Satuan</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['satuan'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Nomor Dokumen</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['nomor_dokumen'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Status</div>
                            <div>
                                @php($st = $detail['status'] ?? null)
                                @if($st)
                                    <span @class([
                                        'inline-flex px-2 py-1 text-xs rounded-full',
                                        'bg-gray-200 text-gray-800' => $st === 'draft',
                                        'bg-green-100 text-green-800' => $st === 'final',
                                    ])>
                                        {{ ucfirst($st) }}
                                    </span>
                                @else
                                    <span class="text-gray-900 dark:text-white font-medium">-</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-500">Total Rencana</div>
                            <div class="text-gray-900 dark:text-white font-medium">Rp {{ number_format((float) ($detail['total_rencana'] ?? 0), 2, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-md font-semibold text-gray-900 dark:text-white mb-2">Items</h4>
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Uraian</th>
                                        <th class="px-3 py-2 text-right">Qty</th>
                                        <th class="px-3 py-2 text-right">Harga Satuan</th>
                                        <th class="px-3 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailItems as $row)
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                            <td class="px-3 py-2">{{ $row['uraian'] }}</td>
                                            <td class="px-3 py-2 text-right">{{ number_format((float) $row['qty'], 2, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-right">Rp {{ number_format((float) $row['harga_satuan'], 2, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-right">Rp {{ number_format((float) $row['subtotal'], 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-2 text-center text-gray-500">Tidak ada item</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="button" wire:click="closeViewModal"
                            class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white px-4 py-2 rounded">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
