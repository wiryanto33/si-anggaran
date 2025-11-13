<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Menunggu Aktivasi')"
        :description="__('Akun Anda telah dibuat, namun belum aktif. Silakan menunggu hingga administrator mengaktifkan akun Anda.')" />

    <div class="flex flex-col gap-4 items-center text-center">
        <div class="max-w-xl w-full rounded-md border border-zinc-200 bg-zinc-50 px-4 py-3 text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
            {{ __('Terima kasih telah mendaftar! Akun Anda saat ini menunggu aktivasi oleh administrator.') }}
        </div>

        <div class="flex items-center justify-center gap-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="filled">{{ __('Log out') }}</flux:button>
            </form>
            <flux:button :href="route('activation.pending')" wire:navigate>{{ __('Refresh') }}</flux:button>
        </div>
    </div>
</div>
