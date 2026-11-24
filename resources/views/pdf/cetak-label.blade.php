<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  @page { size: A4 portrait; margin: 10mm 8mm; }
  * { box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; color: #111; margin: 0; }
  table.grid { width: 100%; border-collapse: separate; border-spacing: 3px; table-layout: fixed; }
  table.grid td { width: 20%; vertical-align: top; padding: 0; }
  .label {
    border: 0.5pt dashed #999;
    border-radius: 4px;
    padding: 2px;
    text-align: center;
    height: 78px;
    overflow: hidden;
  }
  table.top { width: 100%; border-collapse: collapse; table-layout: fixed; }
  table.top td { vertical-align: middle; padding: 0; }
  td.logo { width: auto; text-align: left; }
  .logo img { height: 30px; }
  td.qr { width: 40px; text-align: right; }
  .qr img { width: 38px; height: 38px; }
  .kode { font-size: 5pt; line-height: 1.15; margin-top: 2px; word-break: break-all; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; }
  .nama { font-size: 5.5pt; line-height: 1.2; margin-top: 1px; word-wrap: break-word; overflow-wrap: break-word; }
  .url { font-size: 4.5pt; color: #888; margin-top: 1px; word-break: break-all; }
  .cut { font-size: 4pt; color: #aaa; margin-top: 1px; }
</style>
</head>
<body>
  <table class="grid">
    @forelse($items as $row)
      <tr>
        @foreach($row as $aset)
          <td>
            <div class="label">
              <table class="top">
                <tr>
                  <td class="logo">
                    @if(!empty($aset->logo_data_uri))
                      <div class="logo"><img src="{{ $aset->logo_data_uri }}" alt="Logo"></div>
                    @endif
                  </td>
                  <td class="qr"><div class="qr"><img src="{{ $aset->qr_data_uri }}" alt="QR"></div></td>
                </tr>
              </table>
              <div class="kode" @if(strlen($aset->nomor_label ?? '') > 28) style="font-size: 4.2pt;" @endif>{{ $aset->nomor_label }}</div>
              <div class="nama">{{ \Illuminate\Support\Str::limit($aset->aset_nama, 45) }}</div>
            </div>
          </td>
        @endforeach
        @for($i = $row->count(); $i < 5; $i++)
          <td></td>
        @endfor
      </tr>
    @empty
      <tr><td colspan="5">Tidak ada data aset.</td></tr>
    @endforelse
  </table>
</body>
</html>
