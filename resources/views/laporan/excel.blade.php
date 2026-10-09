<table border="1">
    <thead>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN BULANAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 12px;">
                Periode: {{ $daftarBulan[$bulan] }} {{ $tahun }}
                @if(request('bidang'))
                    — Bidang: {{ request('bidang') === 'cctv' ? 'CCTV' : 'Komputer' }}
                @endif
                @if($user->isAnyTeknisi())
                    — Teknisi: {{ $user->display_name }}
                @endif
            </th>
        </tr>
        <tr><td colspan="12"></td></tr>

        {{-- A. STATISTIK --}}
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">A. STATISTIK</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">Total</th>
            <th style="background-color: #e0e0e0;">Ditugaskan</th>
            <th style="background-color: #e0e0e0;">Dikerjakan</th>
            <th style="background-color: #e0e0e0;">Selesai</th>
        </tr>
        <tr>
            <td style="text-align: center;">{{ $stats['total'] }}</td>
            <td style="text-align: center;">{{ $stats['ditugaskan'] }}</td>
            <td style="text-align: center;">{{ $stats['dikerjakan'] }}</td>
            <td style="text-align: center;">{{ $stats['selesai'] }}</td>
        </tr>
        <tr><td colspan="12"></td></tr>

        {{-- B. REKAP PER TEKNISI --}}
        @if($rekapTeknisi->count() > 0)
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">B. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">No</th>
            <th style="background-color: #e0e0e0;">Nama Teknisi</th>
            <th style="background-color: #e0e0e0;">Total Tugas</th>
            <th style="background-color: #e0e0e0;">Selesai</th>
        </tr>
        @foreach($rekapTeknisi as $i => $r)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $r->teknisi->display_name ?? '-' }}</td>
            <td style="text-align: center;">{{ $r->total }}</td>
            <td style="text-align: center;">{{ $r->selesai }}</td>
        </tr>
        @endforeach
        <tr><td colspan="12"></td></tr>
        @endif

        {{-- C. DETAIL PEKERJAAN --}}
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">C. DETAIL PEKERJAAN</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">No</th>
            <th style="background-color: #e0e0e0;">Kode</th>
            <th style="background-color: #e0e0e0;">Bidang</th>
            <th style="background-color: #e0e0e0;">Tanggal</th>
            <th style="background-color: #e0e0e0;">Judul</th>
            <th style="background-color: #e0e0e0;">Teknisi</th>
            <th style="background-color: #e0e0e0;">Lokasi</th>
            <th style="background-color: #e0e0e0;">Perangkat</th>
            <th style="background-color: #e0e0e0;">Prioritas</th>
            <th style="background-color: #e0e0e0;">Status</th>
            <th style="background-color: #e0e0e0;">Hasil Pekerjaan</th>
            <th style="background-color: #e0e0e0;">Catatan</th>
        </tr>
        @forelse($jobs as $i => $j)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $j->kode_job }}</td>
            <td>{{ $j->bidangLabel() }}</td>
            <td>{{ $j->tanggal_tugas?->format('d-m-Y') }}</td>
            <td>{{ $j->judul }}</td>
            <td>{{ $j->teknisi->display_name ?? '-' }}</td>
            <td>{{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}</td>
            <td>{{ $j->computer_name ?? '-' }} ({{ $j->merk ?? '-' }})</td>
            <td>{{ $j->prioritas }}</td>
            <td>{{ $j->status }}</td>
            <td>{{ $j->hasil_pekerjaan ?? '-' }}</td>
            <td>{{ $j->catatan ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="12" style="text-align: center;">Tidak ada data</td></tr>
        @endforelse

        <tr><td colspan="12"></td></tr>
        <tr>
            <td colspan="12" style="text-align: center;">
                Dicetak pada: {{ now()->format('d-m-Y H:i') }}
            </td>
        </tr>
    </thead>
</table>