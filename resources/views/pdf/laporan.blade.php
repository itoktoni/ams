<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }} {{ $periode ?? '' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        h2 { margin: 0 0 2px; font-size: 16px; }
        p.sub { margin: 0 0 10px; color: #666; }
        .aggs { margin-bottom: 10px; }
        .aggs span { display: inline-block; border: 1px solid #ccc; border-radius: 6px; padding: 4px 10px; margin-right: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p class="sub">Periode: {{ $periode ?? 'Semua' }} • Dicetak {{ now()->format('d/m/Y H:i') }}</p>
    <div class="aggs">
        @foreach($aggs as $a)<span><b>{{ $a['label'] }}:</b> {{ $a['value'] }}</span>@endforeach
    </div>
    <table>
        <thead><tr>@foreach($headers as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
        <tbody>
            @foreach($rows as $row)
            <tr>@foreach($row as $cell)<td>{{ strip_tags((string)$cell) }}</td>@endforeach</tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
