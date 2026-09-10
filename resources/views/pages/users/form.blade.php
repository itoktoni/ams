<?php /** @var App\Models\Users $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="name" />
                <x-input col="6" name="email" />
                <x-input col="6" type="password" name="password" />
                <x-select col="6" name="role" :options="$role"/>

                <x-input col="6" name="phone" placeholder="08xxxxxxxxxx" />

                <div class="col-span-12">
                    <label class="font-body-sm text-body-sm font-bold text-on-surface-variant block mb-1">Telegram Chat ID</label>
                    <div class="flex gap-2">
                        <input type="text" name="telegram_id" value="{{ old('telegram_id', $model?->telegram_id ?? '') }}" placeholder="123456789" class="flex-1 h-12 px-4 bg-white border {{ $errors->has('telegram_id') ? 'border-error' : 'border-outline-variant' }} rounded-lg focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none font-body-sm">
                        <a href="https://t.me/userinfobot" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 h-12 px-4 rounded-lg bg-[#229ED9] text-white text-sm font-semibold hover:bg-[#1d8ac0] transition-colors shrink-0">
                            <span class="material-symbols-outlined text-lg">send</span> @userinfobot
                        </a>
                    </div>
                    @if($errors->has('telegram_id'))<span class="font-label-caps text-label-caps text-error mt-1 block">{{ $errors->first('telegram_id') }}</span>@else<span class="font-label-caps text-label-caps text-on-surface-variant mt-1 block">Klik @userinfobot → Start → copy ID → paste & Save. Lihat juga <a href="{{ route('profile.edit') }}" wire:navigate class="text-primary underline">Settings → Profile</a></span>@endif
                </div>

                <x-file
                    name="avatar"
                    label="Foto Profil"
                    col="12"
                    accept="image/*"
                    capture="environment"
                    :preview="true"
                    :value="$model?->avatar_url"
                    helper="Ambil foto via kamera di HP atau pilih dari galeri" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
