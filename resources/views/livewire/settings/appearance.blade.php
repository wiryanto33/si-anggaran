<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading="__('Manage global look and feel')">
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <div class="grid gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Background Image</label>
                <div class="mt-2 flex items-start gap-4">
                    <div class="relative w-full">
                        @php($bgName = $background ? $background->getClientOriginalName() ?? '1 file selected' : null)
                        <input type="text" readonly value="{{ $bgName ?? '' }}" placeholder="No file chosen"
                            class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 pr-24 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                        <label for="bgFile"
                            class="absolute inset-y-0 right-0 m-1 inline-flex items-center justify-center rounded-md bg-zinc-200 px-3 text-sm font-medium text-zinc-800 dark:bg-zinc-700 dark:text-white">
                            Browse
                        </label>
                        <input id="bgFile" type="file" wire:model="background" accept="image/*" class="sr-only" />
                    </div>

                    <flux:button wire:click="save" variant="primary">Save</flux:button>
                    <flux:button wire:click="clear" variant="ghost">Clear</flux:button>
                </div>
                @error('background')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                <div wire:loading wire:target="background" class="mt-2 text-sm text-gray-500">Uploading...</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Overlay Darkness
                    (0-80%)</label>
                <div class="mt-2 flex items-center gap-4">
                    <input type="range" min="0" max="80" step="1" wire:model.live="overlay"
                        class="w-full" />
                    <span class="text-sm text-gray-600 dark:text-gray-300 w-10 text-right">{{ $overlay }}%</span>
                    <flux:button wire:click="saveOverlay" size="sm">Save</flux:button>
                </div>
                @error('overlay')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preview</label>
                <div class="mt-2 rounded border border-gray-200 dark:border-gray-700 overflow-hidden">
                    @php($__alpha = max(0, min(0.8, ($overlay ?? 30) / 100)))
                    <div class="h-56 w-full bg-center bg-cover" @style($bgUrl ? 'background-image: linear-gradient(rgba(0,0,0,' . $__alpha . '), rgba(0,0,0,' . $__alpha . ')), url(' . $bgUrl . ');' : '')>
                    </div>
                </div>
                @if ($bgUrl)
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $bgUrl }}</div>
                @endif
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login Carousel Images</label>
                <div class="mt-2 flex items-start gap-4">
                    <div class="relative w-full">
                        @php($loginNames = !empty($loginImages) ? count($loginImages) . ' file(s) selected' : null)
                        <input type="text" readonly value="{{ $loginNames ?? '' }}" placeholder="No files chosen"
                            class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 pr-24 text-sm dark:border-zinc-700 dark:bg-zinc-800 dark:text-white" />
                        <label for="loginFiles"
                            class="absolute inset-y-0 right-0 m-1 inline-flex items-center justify-center rounded-md bg-zinc-200 px-3 text-sm font-medium text-zinc-800 dark:bg-zinc-700 dark:text-white">
                            Browse
                        </label>
                        <input id="loginFiles" type="file" wire:model="loginImages" multiple accept="image/*"
                            class="sr-only" />
                    </div>

                    <flux:button wire:click="saveLoginImages" variant="primary">Upload</flux:button>
                </div>
                <div wire:loading wire:target="loginImages" class="mt-2 text-sm text-gray-500">Uploading...</div>
                @error('loginImages.*')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Login Overlay
                        (0-80%)</label>
                    <div class="mt-2 flex items-center gap-4">
                        <input type="range" min="0" max="80" step="1"
                            wire:model.live="loginOverlay" class="w-full" />
                        <span
                            class="text-sm text-gray-600 dark:text-gray-300 w-10 text-right">{{ $loginOverlay }}%</span>
                        <flux:button wire:click="saveLoginOverlay" size="sm">Save</flux:button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                    @forelse($currentLoginImages as $idx => $img)
                        @php($url = asset('storage/' . $img))
                        <div class="relative group border rounded overflow-hidden">
                            <img src="{{ $url }}" alt="login image {{ $idx + 1 }}"
                                class="w-full h-28 object-cover" />
                            <div
                                class="absolute inset-x-0 top-1 flex justify-between px-1 opacity-0 group-hover:opacity-100 transition">
                                <button type="button" wire:click="moveLoginImageUp({{ $idx }})"
                                    class="bg-white/90 text-black text-xs px-2 py-0.5 rounded">Up</button>
                                <button type="button" wire:click="moveLoginImageDown({{ $idx }})"
                                    class="bg-white/90 text-black text-xs px-2 py-0.5 rounded">Down</button>
                            </div>
                            <button type="button" wire:click="removeLoginImage({{ $idx }})"
                                class="absolute bottom-1 right-1 bg-red-600/90 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">Remove</button>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500 dark:text-gray-400">Belum ada gambar diatur. Default akan
                            digunakan.</div>
                    @endforelse
                </div>
            </div>




        </div>
    </x-settings.layout>
</section>
