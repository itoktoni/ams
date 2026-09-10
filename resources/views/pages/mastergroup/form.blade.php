<?php /** @var App\Models\MasterGroup $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-input col="6" name="master_group_kode" helper="Kosongkan = auto (GRP-...)" placeholder="Kosongkan = auto" />
                <x-input col="6" name="master_group_nama" />
                <x-select col="6" name="master_group_status" :options="['aktif'=>'Aktif','nonaktif'=>'Nonaktif']" />
                <x-textarea col="12" name="master_group_keterangan" />

            @endbind
        </x-card>

        <x-card label="Daftar Teknisi dalam Group" icon="engineering">
            @php $linked = $linkedUserIds ?? $linkedTeknisiIds ?? []; @endphp
            <div class="col-span-12">
                <p class="text-xs text-on-surface-variant mb-2">Pilih user dengan role <b>teknisi</b> yang masuk dalam group ini — diambil dari <code>users</code> (1 table), bukan <code>teknisi</code> terpisah. Nanti tiket dari aset yang memakai group ini akan notifikasi ke semua user di sini.</p>
                <select name="user_ids[]" multiple id="select-user_ids" class="w-full h-12 bg-transparent font-body-sm search">
                    @foreach(App\Models\User::where('role','teknisi')->orderBy('name')->get(['id','name','email','telegram_id']) as $u)
                    <option value="{{ $u->id }}" {{ in_array($u->id, $linked, true) ? 'selected' : '' }}>{{ $u->name }} — {{ $u->email }} {{ $u->telegram_id ? '• Telegram: '.$u->telegram_id : '• belum set Telegram' }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-on-surface-variant mt-2">Jika aset memilih group ini, tiket baru akan broadcast ke semua user teknisi di atas via Telegram (users.telegram_id) & Alert. Atur Telegram ID di <a href="{{ route('user.getTable') }}" class="text-primary underline">Users</a> atau <a href="{{ route('profile.edit') }}" class="text-primary underline">Profile</a> → @userinfobot.</p>
            </div>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>
