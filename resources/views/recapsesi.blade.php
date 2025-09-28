<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Guru & Sesi Belajar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* gaya biasa: garis tabel sederhana, tanpa warna */
    .table { border-collapse: collapse; width: 100%; }
    .table th, .table td { border: 1px solid #ccc; padding: .6rem .7rem; vertical-align: top; }
    .table th { font-weight: 600; }
    ul.sesi-list { list-style: none; padding: 0; margin: 0; }
    ul.sesi-list li { margin: 0 0 .35rem 0; }
    .muted { color: #555; font-size: .92rem; }
  </style>
</head>
<body>
<div class="container mt-4">
  <h3 class="mb-3">Rekap Guru &amp; Sesi Belajar</h3>

  <table class="table">
    <thead>
      <tr>
        <th style="width:60px">#</th>
        <th>Guru</th>
        <th style="width:160px">Jumlah Sesi</th>
        <th>Sesi | Kelas | Waktu</th>
      </tr>
    </thead>
    <tbody>
    @forelse ($gurus as $guru)
      <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $guru->name ?? $guru->nama }}</td>
        <td>{{ $guru->sesi_belajars_count ?? $guru->sesiBelajars->count() }}</td>
        <td>
          @if ($guru->sesiBelajars->isEmpty())
            <span class="muted">Belum ada sesi</span>
          @else
            <ul class="sesi-list">
              @foreach ($guru->sesiBelajars as $s)
                <li>
                  {{ $s->judul }}
                <span class="muted">
    | {{ $s->kelas->nama ?? '–' }}
    | {{ $s->created_at ? $s->created_at->locale('id')->isoFormat('D MMM YYYY (dddd) HH:mm') : '—' }}
  </span>
                </li>
              @endforeach
            </ul>
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="4" class="text-center">Tidak ada data guru.</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>
</body>
</html>