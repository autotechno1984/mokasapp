<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Buat akun tenant')" :description="__('Isi data tenant dan admin untuk mulai trial 7 hari')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Data Tenant</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Informasi ini dipakai untuk workspace bisnis Anda.</p>

                <div class="mt-4 flex flex-col gap-4">
                    <flux:input
                        name="nama_tenant"
                        label="Nama tenant"
                        :value="old('nama_tenant')"
                        type="text"
                        required
                        autofocus
                        placeholder="Contoh: Moka Motor"
                    />

                    <flux:input
                        name="subdomain"
                        label="Subdomain"
                        :value="old('subdomain')"
                        type="text"
                        required
                        autocomplete="off"
                        placeholder="moka-motor"
                    />
                    <p class="-mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                        URL tenant: <span class="font-medium">subdomain.{{ config('app.domain') }}</span>
                    </p>

                    <flux:input
                        name="jenis_usaha"
                        label="Jenis usaha"
                        :value="old('jenis_usaha', 'showroom')"
                        type="text"
                        required
                        placeholder="Contoh: showroom"
                    />
                </div>
            </div>

            <div class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Pengguna admin</p>
                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Akun ini akan menjadi pengguna pertama di tenant.</p>

                <div class="mt-4 flex flex-col gap-4">
                    <flux:input
                        name="name"
                        label="Nama lengkap"
                        :value="old('name')"
                        type="text"
                        required
                        autocomplete="name"
                        placeholder="Nama admin"
                    />

                    <flux:input
                        name="email"
                        label="Email"
                        :value="old('email')"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                    />

                    <flux:input
                        name="password"
                        label="Kata sandi"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        viewable
                    />

                    <flux:input
                        name="password_confirmation"
                        label="Konfirmasi kata sandi"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                        viewable
                    />
                </div>
            </div>

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Buat akun & mulai trial') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Masuk') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
