<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\BukuPenyusutan;

class BukuPenyusutanController extends Controller
{
    use ControllerTrait;

    public function __construct(BukuPenyusutan $model)
    {
        $this->model = $model::getModel();
    }

    public function getTable(GeneralRequest $request)
    {
        $data = $this->getData()->cursorPaginate($request->input('per_page', 25))->withQueryString();

        // Summary khusus jika filter aset aktif ?filters[buku_penyusutan_id_aset][$eq]=1
        $summary = null;
        $filters = $request->input('filters', []);
        $asetId = $filters['buku_penyusutan_id_aset']['$eq'] ?? $filters['buku_penyusutan_id_aset'] ?? null;
        if (is_array($asetId)) $asetId = $asetId['$eq'] ?? null;
        if (! empty($asetId)) {
            $aset = Aset::with('hasKategori','hasLokasi')->find($asetId);
            if ($aset) {
                $last = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)->orderByDesc('buku_penyusutan_periode')->first();
                $total = BukuPenyusutan::where('buku_penyusutan_id_aset', $aset->aset_id)->count();
                $perBulan = (int)$aset->aset_masa_manfaat > 0 ? ((float)$aset->aset_harga_perolehan - (float)$aset->aset_nilai_sisa) / (int)$aset->aset_masa_manfaat : 0;
                $summary = [
                    'aset' => $aset,
                    'total' => $total,
                    'perBulan' => $perBulan,
                    'akumulasi' => $last?->buku_penyusutan_akumulasi ?? 0,
                    'nilaiBuku' => $last?->buku_penyusutan_nilai_buku ?? $aset->aset_harga_perolehan,
                    'lastPeriode' => $last?->buku_penyusutan_periode,
                    'progress' => (int)$aset->aset_masa_manfaat > 0 ? round($total / (int)$aset->aset_masa_manfaat * 100, 1) : 0,
                    'sisaBulan' => max(0, (int)$aset->aset_masa_manfaat - $total),
                ];
            }
        }

        return $this->views($this->template(), [
            'data' => $data,
            'fields' => $this->getFields(),
            'summary' => $summary,
        ]);
    }
}
