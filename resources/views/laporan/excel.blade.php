<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        {{-- HEADER UTAMA --}}
        <tr>
            <th colspan="20" style="text-align: center; font-size: 18px; font-weight: bold; background: #4a90e2; color: white;">
                LAPORAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="20" style="text-align: center; font-size: 13px; background: #e8f0fe;">
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
        <tr><td colspan="20"></td></tr>

        {{-- ============================================ --}}
        {{-- A. REKAP PER BULAN — MENDATAR                 --}}
        {{-- ============================================ --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">A. REKAP PER BULAN — {{ $tahun }}</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 120px;">Keterangan</th>
            @foreach($chartTahunan['labels'] as $label)
                <th style="background: #d0e0ff; text-align: center; width: 60px;">{{ $label }}</th>
            @endforeach
            <th style="background: #b3d1ff; text-align: center; width: 80px; font-weight: bold;">TOTAL</th>
            <th colspan="7" style="background: #d0e0ff;"></th>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Total Pekerjaan</td>
            @foreach($chartTahunan['data'] as $val)
                <td style="text-align: center;">{{ $val }}</td>
            @endforeach
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                {{ array_sum($chartTahunan['data']) }}
            </td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Selesai</td>
            @foreach($chartTahunan['selesai'] as $val)
                <td style="text-align: center;">{{ $val }}</td>
            @endforeach
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                {{ array_sum($chartTahunan['selesai']) }}
            </td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Pending</td>
            @foreach($chartTahunan['data'] as $i => $val)
                <td style="text-align: center;">
                    {{ $val - ($chartTahunan['selesai'][$i] ?? 0) }}
                </td>
            @endforeach
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                {{ array_sum($chartTahunan['data']) - array_sum($chartTahunan['selesai']) }}
            </td>
            <td colspan="7"></td>
        </tr>
        <tr><td colspan="20"></td></tr>

        {{-- ============================================ --}}
        {{-- B. REKAP PER TEKNISI — MENDATAR               --}}
        {{-- ============================================ --}}
        @if($rekapTeknisi->count() > 0)
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">B. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 40px; text-align: center;">No</th>
            <th style="background: #d0e0ff; width: 200px;">Nama Teknisi</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Total</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Selesai</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Pending</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Rasio</th>
            @foreach($chartTahunan['labels'] as $label)
                <th style="background: #d0e0ff; text-align: center; width: 50px; font-size: 10px;">{{ $label }}</th>
            @endforeach
            <th colspan="6" style="background: #d0e0ff;"></th>
        </tr>
        @foreach($rekapTeknisi as $i => $r)
            @php
                $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0;
                $bulanDetail = [];
                for ($m = 1; $m <= 12; $m++) {
                    $count = \App\Models\Job::where('teknisi_id', $r->teknisi_id)
                        ->whereMonth('tanggal_tugas', $m)
                        ->whereYear('tanggal_tugas', $tahun)
                        ->where('status', 'SELESAI')
                        ->count();
                    $bulanDetail[] = $count;
                }
            @endphp
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ $r->teknisi->display_name ?? '-' }}</td>
                <td style="text-align: center;">{{ $r->total }}</td>
                <td style="text-align: center;">{{ $r->selesai }}</td>
                <td style="text-align: center;">{{ $r->pending }}</td>
                <td style="text-align: center;">{{ $rasio }}%</td>
                @foreach($bulanDetail as $val)
                    <td style="text-align: center;">
                        @if($val > 0) {{ $val }} @else - @endif
                    </td>
                @endforeach
                <td colspan="6"></td>
            </tr>
        @endforeach
        <tr style="background: #e8f0fe; font-weight: bold;">
            <td colspan="2" style="text-align: right;">TOTAL</td>
            <td style="text-align: center;">{{ $rekapTeknisi->sum('total') }}</td>
            <td style="text-align: center;">{{ $rekapTeknisi->sum('selesai') }}</td>
            <td style="text-align: center;">{{ $rekapTeknisi->sum('pending') }}</td>
            <td style="text-align: center;">
                @php
                    $totalAll = $rekapTeknisi->sum('total');
                    $selesaiAll = $rekapTeknisi->sum('selesai');
                @endphp
                {{ $totalAll > 0 ? round(($selesaiAll / $totalAll) * 100) : 0 }}%
            </td>
            @foreach($chartTahunan['labels'] as $i => $label)
                <td style="text-align: center;">{{ $chartTahunan['selesai'][$i] ?? 0 }}</td>
            @endforeach
            <td colspan="6"></td>
        </tr>
        <tr><td colspan="20"></td></tr>
        @endif

        {{-- ============================================ --}}
        {{-- C. DETAIL PEKERJAAN                           --}}
        {{-- ============================================ --}}
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">
                C. DETAIL PEKERJAAN ({{ $jobs->count() }} pekerjaan)
            </th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 40px; text-align: center;">No</th>
            <th style="background: #d0e0ff; width: 100px;">Kode</th>
            <th style="background: #d0e0ff; width: 70px;">Bidang</th>
            <th style="background: #d0e0ff; width: 80px;">Tanggal</th>
            <th style="background: #d0e0ff;">Judul</th>
            <th style="background: #d0e0ff; width: 130px;">Teknisi</th>
            <th style="background: #d0e0ff; width: 100px;">Gedung</th>
            <th style="background: #d0e0ff; width: 100px;">Ruang</th>
            <th style="background: #d0e0ff; width: 120px;">Perangkat</th>
            <th style="background: #d0e0ff; width: 80px;">Kategori</th>
            <th style="background: #d0e0ff; width: 70px;">Prioritas</th>
            <th style="background: #d0e0ff; width: 70px;">Status</th>
            <th style="background: #d0e0ff;">Hasil</th>
            <th style="background: #d0e0ff;">Catatan</th>
            <th style="background: #d0e0ff; width: 200px;">📷 Foto Sebelum</th>
            <th style="background: #d0e0ff; width: 200px;">📷 Foto Sesudah</th>
            <th colspan="4" style="background: #d0e0ff;"></th>
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
            {{-- 🆕 FOTO SEBELUM --}}
            <td style="font-size: 9px;">
                @forelse($j->fotoSebelum as $f)
                    <a href="{{ asset($f->nama_file) }}" target="_blank"
                       style="color: #0d6efd; text-decoration: underline;">
                        📷 Foto {{ $loop->iteration }}
                    </a>
                    @if(!$loop->last) <br> @endif
                @empty
                    -
                @endforelse
            </td>
            {{-- 🆕 FOTO SESUDAH --}}
            <td style="font-size: 9px;">
                @forelse($j->fotoSesudah as $f)
                    <a href="{{ asset($f->nama_file) }}" target="_blank"
                       style="color: #0d6efd; text-decoration: underline;">
                        📷 Foto {{ $loop->iteration }}
                    </a>
                    @if(!$loop->last) <br> @endif
                @empty
                    -
                @endforelse
            </td>
            <td colspan="4"></td>
        </tr>
        @empty
        <tr><td colspan="20" style="text-align: center;">Tidak ada data</td></tr>
        @endforelse

        <tr><td colspan="20"></td></tr>
        <tr>
            <td colspan="20" style="text-align: center; font-style: italic; font-size: 11px;">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }}
            </td>
        </tr>
    </thead>
</table>