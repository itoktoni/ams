<?php /** @var App\Models\Alert $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => moduleLabel()], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model" enctype="multipart/form-data">
        <x-card :label="moduleLabel()">
            @bind($model ?? null)

                <x-select col="6" name="alert_tipe" :options="App\Enums\Alert\TipeAlertEnum::getOptions()" />
                <x-select col="6" name="alert_level" :options="App\Enums\Alert\LevelAlertEnum::getOptions()" />
                <x-select col="6" name="alert_status" :options="App\Enums\Alert\StatusAlertEnum::getOptions()" />
                <x-select col="6" name="alert_id_pic" :options="App\Models\User::getOptions()" />

                <x-input col="12" name="alert_judul" />
                <x-textarea col="12" name="alert_pesan" />

                {{-- Asset optional — pilih aset terkait jika ada, kosongkan untuk alert umum --}}
                <x-select col="6" name="alert_id_referensi" :options="App\Models\Aset::getOptions()" placeholder="-- Tanpa Aset (Opsional) --" helper="Opsional — kosongkan jika alert umum / tidak terkait aset spesifik" />
                <x-input col="6" name="alert_tipe_referensi" placeholder="otomatis 'aset' jika pilih aset" helper="Otomatis terisi 'aset' saat pilih aset, kosongkan jika umum" />
                <x-input col="12" name="alert_kunci_dedup" helper="Opsional — kunci dedup untuk cegah duplikat alert" />

                <x-input col="6" type="datetime-local" name="alert_jatuh_tempo" />
                <x-input col="6" type="datetime-local" name="alert_terakhir_kirim" />

                <x-input col="6" type="number" name="alert_level_eskalasi" />

            @endbind
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>

    <script>
    (() => {
        const asetSel = document.querySelector('[name="alert_id_referensi"]');
        const tipeInp = document.querySelector('[name="alert_tipe_referensi"]');
        if (!asetSel || !tipeInp) return;
        const sync = () => { if (asetSel.value) { if (!tipeInp.value) tipeInp.value = 'aset'; } };
        asetSel.addEventListener('change', sync);
        // inisialisasi saat edit (jika ada nilai)
        sync();
    })();
    </script>
</x-layouts::app>
