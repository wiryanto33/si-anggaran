<div class="p-6  min-h-screen">
    <div class="flex justify-between items-center mb-6  p-6 rounded">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">User Management</h2>
        @can('user.create')
            <button wire:click="create"
                class="bg-blue-500 hover:bg-blue-600 dark:bg-blue-700 dark:hover:bg-blue-800 text-white px-4 py-2 rounded">
                Add User
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
            <thead class="">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Foto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Satuan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Roles
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                            @if (!empty($user->avatar))
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                    class="h-10 w-10 rounded-full object-cover">
                            @else
                                <div
                                    class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-semibold text-gray-700 dark:text-gray-200">
                                    {{ $user->initials() }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">{{ $user->name }} <br>
                            {{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white">
                            {{ $user->satuan->nama ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($user->active)
                                <span
                                    class="inline-flex px-2 py-1 text-xs bg-green-100 dark:bg-green-700 text-green-800 dark:text-white rounded-full">Active</span>
                            @else
                                <span
                                    class="inline-flex px-2 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($user->roles as $role)
                                <span
                                    class="inline-flex px-2 py-1 text-xs bg-blue-100 dark:bg-blue-700 text-blue-800 dark:text-white rounded-full">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @can('user.edit')
                                <button wire:click="edit({{ $user->id }})"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-2">
                                    Edit
                                </button>
                            @endcan
                            @can('user.delete')
                                <button wire:click="delete({{ $user->id }})" onclick="return confirm('Are you sure?')"
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
        {{ $users->links() }}
    </div>

    <!-- Modal -->
    @if ($showModal)
        <div class="fixed inset-0  backdrop-blur-md overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ $isEditing ? 'Edit User' : 'Add User' }}
                    </h3>

                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" wire:model="name"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" wire:model="email"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Avatar</label>
                            <input type="file" wire:model="avatar"
                                accept="image/png, image/jpeg, image/jpg, image/gif"
                                class="mt-1 block w-full text-sm text-gray-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 dark:file:bg-gray-700 file:text-blue-700 dark:file:text-white hover:file:bg-blue-100 dark:hover:file:bg-gray-600">
                            @error('avatar')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @if ($avatar)
                                <div class="mt-2">
                                    <img src="{{ $avatar->temporaryUrl() }}" alt="Preview"
                                        class="h-12 w-12 rounded-full object-cover">
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input type="password" wire:model="password"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @error('password')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Satuan</label>
                            <select wire:model="satuan_id"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">-- None --</option>
                                @foreach ($satuans as $satuan)
                                    <option value="{{ $satuan->id }}">{{ $satuan->nama }} ({{ $satuan->kode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('satuan_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="active"
                                    class="rounded border-gray-300 dark:border-gray-600">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            @error('active')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                            <select multiple wire:model="selectedRoles"
                                class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
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
