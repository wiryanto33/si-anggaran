<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

@php
    $__bgPath = \App\Models\Setting::get('background_image');
    $__bgOverlay = (int) (\App\Models\Setting::get('background_overlay', 30));
    $__alpha = max(0, min(0.8, $__bgOverlay/100));
@endphp
<body class="min-h-screen bg-white dark:bg-zinc-800"
    @if($__bgPath)
        style="background-image: linear-gradient(rgba(0,0,0,{{ $__alpha }}), rgba(0,0,0,{{ $__alpha }})), url('{{ asset('storage/' . $__bgPath) }}'); background-position: center; background-size: cover; background-attachment: fixed; background-repeat: no-repeat;"
    @endif
>
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="flex w-full items-center justify-center py-4" wire:navigate>
            <img src="{{ asset('img/koarmada.png') }}" alt="Koarmada" class="h-35 w-auto" />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>


                @can('satuan.view')
                    <flux:navlist.item icon="cube" :href="route('satuans.index')"
                        :current="request()->routeIs('satuans.index')" wire:navigate>{{ __('Satuan Management') }}
                    </flux:navlist.item>
                @endcan

                @can('proposal.view')
                    <flux:navlist.item icon="cube" :href="route('proposals.index')"
                        :current="request()->routeIs('proposals.index')" wire:navigate>{{ __('Proposal Management') }}
                    </flux:navlist.item>
                @endcan

                @can('pengumuman.view')
                    <flux:navlist.item icon="megaphone" :href="route('pengumuman.index')"
                        :current="request()->routeIs('pengumuman.index')" wire:navigate>{{ __('Pengumuman Management') }}
                    </flux:navlist.item>
                @endcan

                {{-- @can('annualbudget.view')
                    <flux:navlist.item icon="cube" :href="route('annualbudgets.index')"
                        :current="request()->routeIs('annualbudgets.index')" wire:navigate>{{ __('Annual Budget') }}
                    </flux:navlist.item>
                @endcan

                @can('approval.view')
                    <flux:navlist.item icon="cube" :href="route('approvals.index')"
                        :current="request()->routeIs('approvals.index')" wire:navigate>{{ __('Approvals') }}
                    </flux:navlist.item>
                @endcan --}}

                @canany(['user.view', 'role.view'])
                    <flux:navlist.group :heading="__('Pengguna')" expandable>
                        @can('user.view')
                            <flux:navlist.item icon="user-group" :href="route('users.index')"
                                :current="request()->routeIs('users.index')" wire:navigate>{{ __('User Management') }}
                            </flux:navlist.item>
                        @endcan
                        @can('role.view')
                            <flux:navlist.item icon="queue-list" :href="route('roles.index')"
                                :current="request()->routeIs('roles.index')" wire:navigate>{{ __('Roles Management') }}
                            </flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcanany


            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        {{-- <flux:navlist variant="outline">
            <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Repository') }}
            </flux:navlist.item>

            <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Documentation') }}
            </flux:navlist.item>
        </flux:navlist> --}}

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @php($__avatarUrl = auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : null)
                                @if ($__avatarUrl)
                                    <img src="{{ $__avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-lg object-cover">
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                @php($__avatarUrl = auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : null)
                                @if ($__avatarUrl)
                                    <img src="{{ $__avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-lg object-cover">
                                @else
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                @endif
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
