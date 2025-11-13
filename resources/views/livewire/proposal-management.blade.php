{{-- resources/views/livewire/proposal-management.blade.php --}}

<div class="p-6 min-h-screen">
    <div class="flex justify-between items-center mb-6 p-5 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Proposal Management</h2>
        @can('proposal.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Add Proposal
            </button>
        @endcan
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.500ms="search" placeholder="Cari proposal..."
                class="w-full md:w-80 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600 dark:text-gray-300">Per halaman</span>
                <select wire:model="perPage"
                    class="border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        @if(!empty($canBulkDownload) && $canBulkDownload)
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600 dark:text-gray-300">Terpilih: {{ count($selected) }}</span>
            <button wire:click="downloadSelected" wire:loading.attr="disabled"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded">
                Download Selected (PDF)
            </button>
        </div>
        @endif
        </div>

    <!-- Mobile list (<= md) -->
    <div class="md:hidden space-y-3">
        @foreach ($proposals as $p)
            <div class="rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-center gap-2">
                        @if(!empty($canBulkDownload) && $canBulkDownload)
                            <input type="checkbox" value="{{ $p->id }}" wire:model.live="selected" @disabled($p->status !== 'disetujui') />
                        @endif
                        <div class="font-medium text-gray-900 dark:text-white">#{{ $proposals->firstItem() + $loop->index }} — {{ $p->judul }}</div>
                    </div>
                    <span @class([
                        'shrink-0 inline-flex px-2 py-0.5 text-xs rounded-full',
                        'bg-gray-200 text-gray-800' => $p->status === 'draft',
                        'bg-blue-100 text-blue-800' => $p->status === 'diajukan',
                        'bg-yellow-100 text-yellow-800' => $p->status === 'diverifikasi',
                        'bg-green-100 text-green-800' => $p->status === 'disetujui',
                        'bg-red-100 text-red-800' => $p->status === 'ditolak',
                    ])>{{ ucfirst($p->status) }}</span>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-gray-700 dark:text-gray-300">
                    <div><span class="text-gray-500">Satuan:</span> {{ $p->satuan->nama ?? '-' }}</div>
                    <div><span class="text-gray-500">Perencana:</span> {{ $p->perencana->name ?? '-' }}</div>
                    <div><span class="text-gray-500">Tahun:</span> {{ $p->tahun }}</div>
                    <div><span class="text-gray-500">Tanggal:</span> {{ optional($p->tanggal_pengajuan)->format('Y-m-d') }}</div>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-sm text-gray-900 dark:text-white font-semibold">Rp {{ number_format($p->total_rencana, 2, ',', '.') }}</div>
                    <div class="space-x-3 text-sm font-medium">
                        @can('proposal.view')
                            <button type="button" wire:click="viewProposal({{ $p->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View</button>
                        @endcan
                        @if($p->status === 'disetujui')
                            <a href="{{ route('proposals.pdf', $p->id) }}" target="_blank" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">PDF</a>
                        @endif
                        @can('proposal.edit')
                            <button type="button" wire:click="edit({{ $p->id }})" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</button>
                        @endcan
                        @can('proposal.delete')
                            <button type="button" wire:click="delete({{ $p->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Desktop table (>= md) -->
    <div class="hidden md:block bg-gray-100 dark:bg-gray-800 rounded shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th> --}}
                    @if(!empty($canBulkDownload) && $canBulkDownload)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perencana</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($proposals as $p)
                    <tr>
                        {{-- <td class="px-6 py-4 whitespace-nowrap">{{ $p->kode_usulan }}</td> --}}
                        @if(!empty($canBulkDownload) && $canBulkDownload)
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $p->id }}" wire:model.live="selected" @disabled($p->status !== 'disetujui') />
                            </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap">{{ $proposals->firstItem() + $loop->index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $p->judul }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $p->satuan->nama ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $p->perencana->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $p->tahun }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ optional($p->tanggal_pengajuan)->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span @class([
                                'inline-flex px-2 py-1 text-xs rounded-full',
                                'bg-gray-500 text-gray-800' => $p->status === 'draft',
                                'bg-blue-500 text-blue-800' => $p->status === 'diajukan',
                                'bg-yellow-500 text-yellow-800' => $p->status === 'diverifikasi',
                                'bg-green-500 text-green-800' => $p->status === 'disetujui',
                                'bg-red-500 text-red-800' => $p->status === 'ditolak',
                            ])>
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($p->total_rencana, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('proposal.view')
                                <button type="button" wire:click="viewProposal({{ $p->id }})"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 mr-2">
                                    View
                                </button>
                            @endcan
                            @if($p->status === 'disetujui')
                                <a href="{{ route('proposals.pdf', $p->id) }}" target="_blank"
                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 mr-2">
                                    PDF
                                </a>
                            @endif
                            @can('proposal.edit')
                                <button type="button" wire:click="edit({{ $p->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('proposal.delete')
                                <button type="button" wire:click="delete({{ $p->id }})"
                                    onclick="return confirm('Are you sure?')"
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
    </div>

    <div class="mt-4 text-gray-900 dark:text-white">
        {{ $proposals->links() }}
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div
                class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $isEditing ? 'Edit Proposal' : 'Add Proposal' }}
                    </h3>

                    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kode
                                Usulan</label>
                            <input type="text" wire:model="kode_usulan"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('kode_usulan')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                            <input type="text" wire:model="judul"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('judul')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                            @if (auth()->user()->hasAnyRole(['Perencana', 'perencana']))
                                <input type="text"
                                    value="{{ optional($satuanOptions->first())->nama }} ({{ optional($satuanOptions->first())->kode }})"
                                    disabled
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            @else
                                <select wire:model="satuan_id"
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach ($satuanOptions as $opt)
                                        <option value="{{ $opt->id }}">{{ $opt->nama }} ({{ $opt->kode }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('satuan_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Perencana</label>
                            @if (auth()->user()->hasAnyRole(['Perencana', 'perencana']))
                                <input type="text" value="{{ auth()->user()->name }}" disabled
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            @else
                                <select wire:model="perencana_id"
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">-- Pilih Perencana --</option>
                                    @foreach ($userOptions as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('perencana_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tahun</label>
                            <input type="number" wire:model="tahun" min="2000" max="2100"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('tahun')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal
                                Pengajuan</label>
                            <input type="date" wire:model="tanggal_pengajuan"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('tanggal_pengajuan')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                            <textarea wire:model="deskripsi" rows="3"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                            @error('deskripsi')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select wire:model="status"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @foreach ($statusOptions as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total
                                Rencana</label>
                            <input type="number" step="0.01" min="0" wire:model="total_rencana" disabled
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('total_rencana')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        @php($isVerifikator = auth()->check() && auth()->user()->hasAnyRole(['Verifikator','verifikator','Super Admin']))
                        @if($isVerifikator)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                    Verifikator</label>
                                <textarea wire:model="catatan_verifikator" rows="2"
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                @error('catatan_verifikator')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        @php($isPimpinan = auth()->check() && auth()->user()->hasAnyRole(['Pimpinan','pimpinan','Super Admin']))
                        @if($isPimpinan)
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan
                                    Pimpinan</label>
                                <textarea wire:model="catatan_pimpinan" rows="2"
                                    class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                                @error('catatan_pimpinan')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white">Items</h4>
                                <button type="button" wire:click="addItemRow"
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded">+ Add
                                    Item</button>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Uraian</th>
                                            <th class="px-3 py-2 text-right">Qty</th>
                                            <th class="px-3 py-2 text-left">Satuan</th>
                                            <th class="px-3 py-2 text-right">Harga Satuan</th>
                                            <th class="px-3 py-2 text-right">Subtotal</th>
                                            <th class="px-3 py-2 text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $i => $row)
                                            <tr class="border-t border-gray-200 dark:border-gray-700"
                                                wire:key="pi-{{ $i }}">
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                        wire:model="items.{{ $i }}.uraian"
                                                        class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="number" step="0.01" min="0"
                                                        wire:model.live="items.{{ $i }}.qty"
                                                        wire:input="onItemChange({{ $i }})"
                                                        class="w-full text-right border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="text"
                                                        wire:model="items.{{ $i }}.satuan"
                                                        class="w-full border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </td>
                                                <td class="px-3 py-2">
                                                    <input type="number" step="0.01" min="0"
                                                        wire:model.live="items.{{ $i }}.harga_satuan"
                                                        wire:input="onItemChange({{ $i }})"
                                                        class="w-full text-right border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </td>
                                                <td class="px-3 py-2 text-right text-gray-900 dark:text-white">
                                                    Rp
                                                    {{ number_format((float) ($row['subtotal'] ?? 0), 2, ',', '.') }}
                                                </td>
                                                <td class="px-3 py-2 text-center">
                                                    <button type="button"
                                                        wire:click="removeItemRow({{ $i }})"
                                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded">x</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex justify-end space-x-2 mt-2">
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

    @if ($showViewModal)
        <div class="fixed inset-0 backdrop-blur-md overflow-y-auto h-full w-full">
            <div
                class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-1">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Detail Proposal</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">Kode Usulan</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['kode_usulan'] ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-500">Judul</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['judul'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Satuan</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['satuan'] ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-500">Perencana</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['perencana'] ?? '-' }}
                            </div>
                        </div>
                        <div>
                            <div class="text-gray-500">Tahun</div>
                            <div class="text-gray-900 dark:text-white font-medium">{{ $detail['tahun'] ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Tanggal Pengajuan</div>
                            <div class="text-gray-900 dark:text-white font-medium">
                                {{ $detail['tanggal_pengajuan'] ?? '-' }}</div>
                        </div>


                        <div>
                            <div class="text-gray-500">Status</div>
                            <div>
                                @php($st = $detail['status'] ?? null)
                                @if ($st)
                                    <span @class([
                                        'inline-flex px-2 py-1 text-xs rounded-full',
                                        'bg-gray-200 text-gray-800' => $st === 'draft',
                                        'bg-blue-100 text-blue-800' => $st === 'diajukan',
                                        'bg-yellow-100 text-yellow-800' => $st === 'diverifikasi',
                                        'bg-green-100 text-green-800' => $st === 'disetujui',
                                        'bg-red-100 text-red-800' => $st === 'ditolak',
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
                            <div class="text-gray-900 dark:text-white font-medium">Rp
                                {{ number_format((float) ($detail['total_rencana'] ?? 0), 2, ',', '.') }}</div>
                        </div>

                        <div>
                            <span class="inline-flex w-fit items-center rounded px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-400/20 dark:text-amber-300">Catatan Verifikator</span>
                            <textarea readonly rows="2"
                                class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">{{ $detail['catatan_verifikator'] ?? '-' }}</textarea>
                        </div>

                        <div>
                            <span class="inline-flex w-fit items-center rounded px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-400/20 dark:text-blue-300">Catatan Pimpinan</span>
                            <textarea readonly rows="2"
                                class="mt-1 block w-full border border-gray-200 dark:border-gray-700 rounded-md px-3 py-2 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">{{ $detail['catatan_pimpinan'] ?? '-' }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <div class="text-gray-500">Deskripsi</div>
                            <div class="text-gray-900 dark:text-white">{{ $detail['deskripsi'] ?? '-' }}</div>
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
                                        <th class="px-3 py-2 text-left">Satuan</th>
                                        <th class="px-3 py-2 text-right">Harga Satuan</th>
                                        <th class="px-3 py-2 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($detailItems as $row)
                                        <tr class="border-t border-gray-200 dark:border-gray-700">
                                            <td class="px-3 py-2">{{ $row['uraian'] }}</td>
                                            <td class="px-3 py-2 text-right">
                                                {{ number_format((float) $row['qty'], 2, ',', '.') }}</td>
                                            <td class="px-3 py-2">{{ $row['satuan'] }}</td>
                                            <td class="px-3 py-2 text-right">Rp
                                                {{ number_format((float) $row['harga_satuan'], 2, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-right">Rp
                                                {{ number_format((float) $row['subtotal'], 2, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-2 text-center text-gray-500">Tidak ada
                                                item</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between">
                        @if(($detail['status'] ?? '') === 'disetujui')
                            <a href="{{ isset($detail['id']) ? route('proposals.pdf', $detail['id']) : '#' }}" target="_blank"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                                Download PDF
                            </a>
                        @endif
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
