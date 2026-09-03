<?php

namespace App\Observers;

use App\Models\Aset;
use App\Services\PenyusutanService;
use Illuminate\Support\Facades\Log;

class AsetObserver
{
    public function created(Aset $aset): void
    {
        $this->sync($aset);
    }

    public function updated(Aset $aset): void
    {
        // hanya sync jika field penyusutan berubah
        if ($aset->wasChanged(['aset_tanggal_mulai_susut','aset_masa_manfaat','aset_harga_perolehan','aset_nilai_sisa','aset_metode_penyusutan'])) {
            $this->sync($aset);
        }
    }

    private function sync(Aset $aset): void
    {
        if (empty($aset->aset_tanggal_mulai_susut) || (int) $aset->aset_masa_manfaat <= 0) {
            return;
        }

        try {
            app(PenyusutanService::class)->jalankan($aset);
        } catch (\Throwable $e) {
            Log::warning('AsetObserver sync gagal: '.$e->getMessage(), ['aset_id' => $aset->aset_id]);
        }
    }
}
