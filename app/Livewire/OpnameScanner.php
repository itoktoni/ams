<?php

namespace App\Livewire;

use App\Models\Aset;
use App\Models\Opname;
use App\Models\OpnameDetail;
use Livewire\Component;

class OpnameScanner extends Component
{
    public int $opnameId;
    public string $kode = '';
    public string $message = '';
    public bool $isSuccess = false;
    public bool $isError = false;

    public function mount(int $opnameId): void
    {
        $this->opnameId = $opnameId;
    }

    public function scan(): void
    {
        $this->resetFeedback();
        $raw = trim($this->kode);
        if ($raw === '') {
            $this->isError = true;
            $this->message = 'Kode kosong.';
            return;
        }

        $kode = $raw;
        if (str_contains($kode, '://') || str_contains($kode, '/')) {
            if (preg_match('/aset\/scan\/([A-Za-z0-9_-]+)/i', $kode, $m)) $kode = $m[1];
            elseif (preg_match('/aset_qr=([A-Za-z0-9_-]+)/i', $kode, $m)) $kode = $m[1];
            else { $parts = explode('/', trim($kode, '/')); $last = end($parts); $kode = explode('?', $last)[0] ?: $kode; $kode = trim($kode); }
        }

        $opname = Opname::find($this->opnameId);
        if (! $opname) { $this->error('Opname tidak ditemukan.'); return; }
        if ($opname->opname_status === 'selesai') { $this->error('Opname sudah selesai.'); return; }

        $aset = Aset::where('aset_kode_qr', $kode)->orWhere('aset_kode', $kode)->first();
        if (! $aset) { $this->error('Aset tidak ditemukan: '.$kode); return; }

        $detail = OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_id_aset', $aset->aset_id)->first();
        if (! $detail) { $this->error('Aset ini tidak termasuk opname lokasi ini: '.$aset->aset_nama); return; }
        if ($detail->opname_detail_ditemukan) {
            $this->isSuccess = true;
            $this->message = 'Sudah discan sebelumnya: '.$aset->aset_nama.' — '.$aset->aset_kode;
            $this->kode = '';
            return;
        }

        $detail->update(['opname_detail_ditemukan' => true, 'opname_detail_waktu_scan' => now(), 'opname_detail_id_petugas_scan' => auth()->id(), 'opname_detail_status_fisik' => $aset->aset_status, 'opname_detail_kondisi' => $aset->aset_kondisi]);
        $opname->update(['opname_total_fisik' => OpnameDetail::where('opname_detail_id_opname', $opname->opname_id)->where('opname_detail_ditemukan', true)->count()]);

        $this->isSuccess = true;
        $this->message = 'Berhasil scan ✓  '.$aset->aset_nama.' — '.$aset->aset_kode;
        $this->kode = '';
    }

    private function resetFeedback(): void
    {
        $this->message = '';
        $this->isSuccess = false;
        $this->isError = false;
    }

    private function error(string $msg): void
    {
        $this->isError = true;
        $this->message = $msg;
    }

    public function render()
    {
        $opname = Opname::with(['hasLokasi', 'hasPetugas'])->findOrFail($this->opnameId);
        $details = OpnameDetail::with(['hasAset'])->where('opname_detail_id_opname', $opname->opname_id)->orderBy('opname_detail_ditemukan', 'desc')->orderBy('opname_detail_id_aset')->get();
        $progress = $opname->progress;
        return view('livewire.opname-scanner', compact('opname', 'details', 'progress'));
    }
}
