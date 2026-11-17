<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Enumerable;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $bulan;
    protected $tahun;

    public function __construct($bulan, $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection(): Enumerable
    {
        $start = now()->setYear($this->tahun)->setMonth($this->bulan)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        return Attendance::rekapQuery()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal', 
            'Nama Teknisi', 
            'Bidang', 
            'Jam Masuk', 
            'Jam Pulang', 
            'Status',
            'Durasi Kerja (Menit)',
            'Keterangan Jam'
        ];
    }

    public function map($row): array
    {
        $user = User::find($row->user_id);
        $status = Attendance::hitungStatus($row->jam_masuk);
        
        $durasiMenit = Attendance::hitungDurasi($row->jam_masuk, $row->jam_pulang);
        $ket = Attendance::hitungKeterangan($row->jam_masuk, $row->jam_pulang);

        return [
            \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y'),
            $user ? $user->display_name : '-',
            $user ? $user->bidangLabel() : '-',
            $row->jam_masuk ? substr($row->jam_masuk, 0, 5) : '-',
            $row->jam_pulang ? substr($row->jam_pulang, 0, 5) : '-',
            strtoupper($status),
            $durasiMenit ?? '-',
            $ket['label'],
        ];
    }
}