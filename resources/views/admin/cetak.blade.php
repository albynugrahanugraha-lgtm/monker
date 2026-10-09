<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan #{{ $job->kode_job }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 13px; text-align: center; margin-top: 0; margin-bottom: 20px; font-weight: normal; }
        h3 { font-size: 13px; margin-top: 20px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; width: 30%; }
        .foto-box { display: inline-block; margin: 5px; text-align: center; }
        .foto-box img { max-width: 200px; max-height: 200px; border: 1px solid #ccc; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { width: 40%; text-align: center; }
        .ttd p { margin: 60px 0 0 0; border-top: 1px solid #000; padding-top: 5px; }
        .no-print { text-align: center; margin: 20px 0; }

        /* 🆕 Badge bidang */
        .badge-cctv { background: #0dcaf0; color: #000; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-komputer { background: #0d6efd; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<h1>LAPORAN PEKERJAAN TEKNISI {{ strtoupper($job->bidangLabel()) }} (ADMIN)</h1>
<h2>Kode: {{ $job->kode_job }}</h2>

<table>
    <tr><th>Judul Pekerjaan</th><td>{{ $job->judul }}</td></tr>
    <tr><th>Deskripsi</th><td>{{ $job->deskripsi ?? '-' }}</td></tr>
    <tr>
        <th>Bidang</th>
        <td>
            <span class="{{ $job->bidang === 'cctv' ? 'badge-cctv' : 'badge-komputer' }}">
                {{ $job->bidangLabel() }}
            </span>
        </td>
    </tr>
    <tr><th>Kategori</th><td>{{ $job->kategori ?? '-' }}</td></tr>
    <tr><th>Prioritas</th><td>{{ $job->prioritas }}</td></tr>
    <tr><th>Status</th><td>{{ $job->status }}</td></tr>
    <tr><th>Lokasi Gedung</th><td>{{ $job->lok_gedung ?? '-' }}</td></tr>
    <tr><th>Lokasi Ruang</th><td>{{ $job->lokasi_ruang ?? '-' }}</td></tr>
    <tr><th>Perangkat</th><td>{{ $job->computer_name ?? '-' }}</td></tr>
    <tr><th>Merk</th><td>{{ $job->merk ?? '-' }}</td></tr>
    <tr><th>Teknisi</th><td>{{ $job->teknisi->display_name ?? '-' }}</td></tr>
    <tr><th>Admin Pembuat</th><td>{{ $job->admin->display_name ?? 'Dibuat sendiri oleh teknisi' }}</td></tr>
    <tr><th>Tanggal Tugas</th><td>{{ $job->tanggal_tugas?->format('d-m-Y') }}</td></tr>
    <tr><th>Tanggal Selesai</th><td>{{ $job->tanggal_selesai?->format('d-m-Y H:i') ?? '-' }}</td></tr>
    <tr><th>Hasil Pekerjaan</th><td>{{ $job->hasil_pekerjaan ?? '-' }}</td></tr>
    <tr><th>Catatan</th><td>{{ $job->catatan ?? '-' }}</td></tr>
</table>

<h3>Foto Sebelum Pekerjaan</h3>
<div>
    @forelse($job->fotoSebelum as $f)
        <div class="foto-box"><img src="{{ asset($f->nama_file) }}"></div>
    @empty
        <p>- Tidak ada foto -</p>
    @endforelse
</div>

<h3>Foto Sesudah Pekerjaan</h3>
<div>
    @forelse($job->fotoSesudah as $f)
        <div class="foto-box"><img src="{{ asset($f->nama_file) }}"></div>
    @empty
        <p>- Tidak ada foto -</p>
    @endforelse
</div>

<div class="ttd">
    <div><p>Teknisi {{ $job->bidangLabel() }}</p></div>
    <div><p>Admin</p></div>
</div>

<div class="no-print">
    <button onclick="window.print()" style="padding:10px 20px; background:#0d6efd; color:white; border:none; border-radius:5px; cursor:pointer;">
        🖨️ Cetak Sekarang
    </button>
</div>

</body>
</html>