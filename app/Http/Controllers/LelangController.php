<?php

namespace App\Http\Controllers;

use App\Models\PenawaranPenjualan;
use App\Models\PenjualanAset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LelangController extends Controller
{
    public function index(Request $request)
    {
        $q = PenjualanAset::with(['hasAset.hasKategori', 'hasPenawaran'])
            ->whereIn('penjualan_aset_status', ['ditawarkan', 'negosiasi', 'disetujui', 'terverifikasi'])
            ->orderByDesc('penjualan_aset_tanggal_request');

        if ($search = $request->input('q')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('penjualan_aset_nomor', 'like', "%{$search}%")
                    ->orWhere('penjualan_aset_alasan', 'like', "%{$search}%")
                    ->orWhereHas('hasAset', fn ($a) => $a->where('aset_nama', 'like', "%{$search}%")->orWhere('aset_kode', 'like', "%{$search}%"));
            });
        }

        $items = $q->paginate(12)->withQueryString();

        // enrich with highest bid & count
        $items->getCollection()->transform(function ($row) {
            $row->highest_bid = $row->hasPenawaran->max('penawaran_penjualan_harga');
            $row->bid_count = $row->hasPenawaran->count();
            return $row;
        });

        return view('lelang.index', compact('items'));
    }

    public function show(int $id)
    {
        $item = PenjualanAset::with(['hasAset.hasKategori', 'hasAset.hasLokasi', 'hasPenawaran.hasUser'])
            ->findOrFail($id);

        $offers = $item->hasPenawaran()->orderByDesc('penawaran_penjualan_harga')->orderBy('penawaran_penjualan_waktu')->get();
        $highest = $offers->max('penawaran_penjualan_harga');
        $minBid = $highest ? (float) $highest + 1000 : (float) ($item->penjualan_aset_harga_appraisal ?? 0);
        if ($minBid <= 0) $minBid = 100000;

        $winner = $offers->first(); // highest

        return view('lelang.show', compact('item', 'offers', 'highest', 'minBid', 'winner'));
    }

    public function bid(Request $request, int $id)
    {
        $item = PenjualanAset::with('hasPenawaran')->findOrFail($id);

        if (! in_array($item->penjualan_aset_status, ['ditawarkan', 'negosiasi', 'disetujui', 'terverifikasi', 'diajukan'])) {
            return back()->withErrors(['harga' => 'Lelang sudah ditutup / tidak menerima penawaran.'])->withInput();
        }

        $request->validate([
            'harga' => 'required|numeric|min:0',
            'kontak' => 'nullable|string|max:60',
        ]);

        $harga = (float) $request->input('harga');
        $highest = $item->hasPenawaran()->max('penawaran_penjualan_harga');
        $minBid = $highest ? (float) $highest + 1000 : (float) ($item->penjualan_aset_harga_appraisal ?? 0);
        if ($minBid <= 0) $minBid = 100000;

        if ($harga < $minBid) {
            return back()->withErrors(['harga' => 'Penawaran minimum Rp ' . number_format($minBid, 0, ',', '.')])->withInput();
        }

        $user = Auth::user();

        PenawaranPenjualan::create([
            'penawaran_penjualan_id_penjualan' => $item->penjualan_aset_id,
            'penawaran_penjualan_id_user' => $user?->id,
            'penawaran_penjualan_nama_pembeli' => $user?->name ?? $request->input('nama', 'Guest'),
            'penawaran_penjualan_kontak' => $request->input('kontak') ?? $user?->email,
            'penawaran_penjualan_harga' => $harga,
            'penawaran_penjualan_tanggal' => now()->toDateString(),
            'penawaran_penjualan_waktu' => now(),
            'penawaran_penjualan_status' => 'diajukan',
            'penawaran_penjualan_hasil' => 'Menunggu verifikasi',
        ]);

        return back()->with('success', 'Penawaran Rp ' . number_format($harga, 0, ',', '.') . ' berhasil dikirim!');
    }
}