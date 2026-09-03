<?php

namespace App\Http\Controllers;

use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Aset;
use App\Models\BukuPenyusutan;

class BukuPenyusutanController extends Controller
{
    use ControllerTrait {
        getData as traitGetData;
    }

    public function __construct(BukuPenyusutan $model)
    {
        $this->model = $model::getModel();
    }

    protected function getData()
    {
        $query = $this->traitGetData();
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user','customer'], true)) {
            $asetIds = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
            if (empty($asetIds)) {
                $query->whereRaw('1=0');
            } else {
                $query->whereIn('buku_penyusutan_id_aset', $asetIds);
            }
            // jika filter aset spesifik tapi bukan miliknya → 403 handled di getTable
        }
        return $query;
    }

    public function getTable(GeneralRequest $request)
    {
        // untuk pengguna_aset, cek filter aset miliknya
        $user = auth()->user();
        if ($user && in_array($user->role, ['pengguna_aset','user'], true)) {
            $filters = $request->input('filters', []);
            $asetId = $filters['buku_penyusutan_id_aset']['$eq'] ?? $filters['buku_penyusutan_id_aset'] ?? null;
            if (is_array($asetId)) $asetId = $asetId['$eq'] ?? null;
            if (! empty($asetId)) {
                $allowed = Aset::where('aset_id_penanggung_jawab', $user->id)->pluck('aset_id')->all();
                if (! in_array((int)$asetId, $allowed, true) && ! in_array($asetId, $allowed, true)) {
                    abort(403, 'Anda hanya boleh melihat penyusutan aset yang di-assign kepada Anda.');
                }
            }
        }

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
