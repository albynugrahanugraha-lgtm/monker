<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        <tr>
            <th colspan="14" style="text-align: center; font-size: 18px; font-weight: bold; background: #4a90e2; color: white;">
                LAPORAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="14" style="text-align: center; font-size: 13px; background: #e8f0fe;">
                Periode:
                @if($bulan && $bulan !== 'semua')
                    {{ $daftarBulan[$bulan] }} {{ $tahun }}
                @else
                    Tahun {{ $tahun }} (Januari - Desember)
                @endif
                @if(request('bidang'))
                    — Bidang: {{ request('bidang') === 'cctv' ? 'CCTV' : 'Komputer' }}
                @endif
            </th>
        </tr>
        <tr><td colspan="14"></td></tr>

        {{-- A. STATISTIK --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">A. STATISTIK</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">Total</th>
            <th style="background: #d0e0ff;">Ditugaskan</th>
            <th style="background: #d0e0ff;">Dikerjakan</th>
            <th style="background: #d0e0ff;">Selesai</th>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold;">{{ $stats['total'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $stats['ditugaskan'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $stats['dikerjakan'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $stats['selesai'] }}</td>
        </tr>
        <tr><td colspan="14"></td></tr>

        {{-- B. REKAP PER BULAN --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">B. REKAP PER BULAN</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 150px;">Bulan</th>
            <th style="background: #d0e0ff; width: 100px;">Total</th>
            <th style="background: #d0e0ff; width: 100px;">Selesai</th>
        </tr>
        @foreach($chartTahunan['labels'] as $i => $label)
        <tr>
            <td>{{ $label }} {{ $tahun }}</td>
            <td style="text-align: center;">{{ $chartTahunan['data'][$i] ?? 0 }}</td>
            <td style="text-align: center;">{{ $chartTahunan['selesai'][$i] ?? 0 }}</td>
        </tr>
        @endforeach
        <tr><td colspan="14"></td></tr>

        {{-- C. REKAP PER TEKNISI --}}
        @if($rekapTeknisi->count() > 0)
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">C. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Nama Teknisi</th>
            <th style="background: #d0e0ff;">Total</th>
            <th style="background: #d0e0ff;">Selesai</th>
            <th style="background: #d0e0ff;">Pending</th>
            <th style="background: #d0e0ff;">Rasio</th>
        </tr>
        @foreach($rekapTeknisi as $i => $r)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $r->teknisi->display_name ?? '-' }}</td>
            <td style="text-align: center;">{{ $r->total }}</td>
            <td style="text-align: center;">{{ $r->selesai }}</td>
            <td style="text-align: center;">{{ $r->pending }}</td>
            <td style="text-align: center;">
                {{ $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0 }}%
            </td>
        </tr>
        @endforeach
        <tr><td colspan="14"></td></tr>
        @endif

        {{-- D. DETAIL PEKERJAAN --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">
                D. DETAIL PEKERJAAN ({{ $jobs->count() }} pekerjaan)
            </th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Kode</th>
            <th style="background: #d0e0ff;">Bidang</th>
            <th style="background: #d0e0ff;">Tanggal</th>
            <th style="background: #d0e0ff;">Judul</th>
            <th style="background: #d0e0ff;">Teknisi</th>
            <th style="background: #d0e0ff;">Gedung</th>
            <th style="background: #d0e0ff;">Ruang</th>
            <th style="background: #d0e0ff;">Perangkat</th>
            <th style="background: #d0e0ff;">Kategori</th>
            <th style="background: #d0e0ff;">Prioritas</th>
            <th style="background: #d0e0ff;">Status</th>
            <th style="background: #d0e0ff;">Hasil</th>
            <th style="background: #d0e0ff;">Catatan</th>
        </tr>
        @forelse($jobs as $i => $j)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $j->kode_job }}</td>
            <td>{{ $j->bidangLabel() }}</td>
            <td style="text-align: center;">{{ $j->tanggal_tugas?->format('d-m-Y') }}</td>
            <td>{{ $j->judul }}</td>
            <td>{{ $j->teknisi->display_name ?? '-' }}</td>
            <td>{{ $j->lok_gedung ?? '-' }}</td>
            <td>{{ $j->lokasi_ruang ?? '-' }}</td>
            <td>{{ $j->computer_name ?? '-' }} ({{ $j->merk ?? '-' }})</td>
            <td>{{ $j->kategori ?? '-' }}</td>
            <td>{{ $j->prioritas }}</td>
            <td>{{ $j->status }}</td>
            <td>{{ $j->hasil_pekerjaan ?? '-' }}</td>
            <td>{{ $j->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="14" style="text-align: center;">Tidak ada data</td></tr>
        @endforelse

        <tr><td colspan="14"></td></tr>

        {{-- E. LINK FOTO --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">E. LINK FOTO PER PEKERJAAN</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Kode</th>
            <th style="background: #d0e0ff;" colspan="5">Foto Sebelum</th>
            <th style="background: #d0e0ff;" colspan="5">Foto Sesudah</th>
        </tr>
        @foreach($jobs as $i => $j)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $j->kode_job }}</td>
            <td colspan="5" style="font-size: 9px;">
                @forelse($j->fotoSebelum as $f)
                    {{ asset($f->nama_file) }}
                    @if(!$loop->last) &nbsp;|&nbsp; @endif
                @empty
                    -
                @endforelse
            </td>
            <td colspan="5" style="font-size: 9px;">
                @forelse($j->fotoSesudah as $f)
                    {{ asset($f->nama_file) }}
                    @if(!$loop->last) &nbsp;|&nbsp; @endif
                @empty
                    -
                @endforelse
            </td>
        </tr>
        @endforeach

        <tr><td colspan="14"></td></tr>
        <tr>
            <td colspan="14" style="text-align: center; font-style: italic; font-size: 11px;">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }}
            </td>
        </tr>
    </thead>
</table>