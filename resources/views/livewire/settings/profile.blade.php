<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <div class="col-span-full">
                <label for="profile-picture" class="block text-sm/6 font-medium text-white">Foto Profile </label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-white/25 px-6 py-6">
                    <div class="text-center">
                        <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true"
                            class="mx-auto size-12 text-gray-600">
                            <path
                                d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <div class="mt-4 flex text-sm/6 text-gray-400">
                            <label for="avatar"
                                class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-400 focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-indigo-500 hover:text-indigo-300">
                                <span>Upload a file</span>
                                <input id="avatar" type="file" name="avatar" class="sr-only" wire:model="avatar"
                                    accept="image/png, image/jpeg, image/jpg" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs/5 text-gray-400">PNG, JPG, 5MB</p>
                        @error('avatar')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="avatar" class="mt-4"
                                class="flex items-center justify-center w-56 h-56 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                                <div role="status">
                                    <svg aria-hidden="true"
                                        class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                            fill="currentColor" />
                                        <path
                                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                            fill="currentFill" />
                                    </svg>
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>

                            @if ($avatar)
                                <div class="mt-3 flex justify-center">
                                    <img src="{{ $avatar->temporaryUrl() }}" alt="Preview"
                                        class="h-16 w-16 rounded-full object-cover border border-white/10" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus
                    autocomplete="name" />

                <div>
                    <flux:input wire:model="email" :label="__('Email')" type="email" required
                        autocomplete="email" />

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                        <div>
                            <flux:text class="mt-4">
                                {{ __('Your email address is unverified.') }}

                                <flux:link class="text-sm cursor-pointer"
                                    wire:click.prevent="resendVerificationNotification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </flux:link>

                            </flux:text>

                            @if (session('status') === 'verification-link-sent')
                                <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </flux:text>
                            @endif
                        </div>
                    @endif
                </div>

                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Satuan') }}</label>
                    <select wire:model="satuan_id"
                        class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800 dark:text-white">
                        <option value="">-- {{ __('Pilih Satuan') }} --</option>
                        @foreach ($satuans as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kode }})</option>
                        @endforeach
                    </select>
                    @error('satuan_id')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                    </div>

                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
