<?php /** @var App\Models\User $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel() . ' (User role teknisi — 1 table)'">
            @bind($model ?? null)

                <input type="hidden" name="role" value="teknisi">
                <x-input col="6" name="name" label="Nama Teknisi" />
                <x-input col="6" name="email" type="email" />
                @if(!isset($model) || !$model->exists)
                    <x-input col="6" type="password" name="password" helper="Default password jika kosong" />
                @endif
                <x-input col="6" name="phone" label="Telepon" placeholder="08xxxxxxxxxx" />
                <x-input col="6" name="telegram_id" label="Telegram Chat ID" placeholder="123456789" helper="OPSIONAL — untuk notifikasi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
