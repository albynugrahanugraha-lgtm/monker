<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class JobsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected string $bidang;

    public function __construct(string $bidang = 'cctv')
    {
        $this->bidang = $bidang;
    }

    public function headings(): array
    {
        if ($this->bidang === 'cctv') {
            return [
                'judul',
                'deskripsi',
                'kategori',
                'prioritas',
                'lokasi_gedung',
                'lokasi_ruang',
                'titik_kamera',
                'channel_dvr',
                'jenis_kamera',
                'nama_perangkat',
                'merk',
                'teknisi_username',
                'tanggal_tugas',
            ];
        }

        return [
            'judul',
            'deskripsi',
            'kategori',
            'prioritas',
            'lokasi_gedung',
            'lokasi_ruang',
            'nama_perangkat',
            'merk',
            'teknisi_username',
            'tanggal_tugas',
        ];
    }

    public function array(): array
    {
        if ($this->bidang === 'cctv') {
            return [
                [
                    'Perbaikan Kamera Lobby',
                    'Kamera mati sejak pagi',
                    'CCTV',
                    'Tinggi',
                    'Gedung A',
                    'Lobby',
                    'Pintu Masuk',
                    'CH-01',
                    'Dome Indoor',
                    'CAM-01',
                    'Hikvision',
                    'teknisi_cctv',
                    date('Y-m-d'),
                ],
                [
                    'Setting DVR Gedung B',
                    'Perlu setting ulang DVR',
                    'CCTV',
                    'Sedang',
                    'Gedung B',
                    'Ruang Server',
                    'Depan Rack',
                    'CH-05',
                    'Bullet',
                    'DVR-01',
                    'Dahua',
                    '',
                    date('Y-m-d', strtotime('+1 day')),
                ],
            ];
        }

        return [
            [
                'Perbaikan PC Lab',
                'PC tidak mau nyala',
                'PC',
                'Sedang',
                'Gedung B',
                'R. IT-01',
                'PC-IT-01',
                'Lenovo',
                'teknisi',
                date('Y-m-d'),
            ],
        ];
    }

    /**
     * 🆕 Return type `: ?array` WAJIB untuk maatwebsite/excel 4.x
     */
    public function styles(Worksheet $sheet): ?array
    {
        // Header style (bold + biru)
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0D6EFD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Baris contoh: warna abu-abu + italic
        $sheet->getStyle('A2:' . $sheet->getHighestColumn() . '3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0'],
            ],
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '666666'],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30, // judul
            'B' => 30, // deskripsi
            'C' => 12, // kategori
            'D' => 12, // prioritas
            'E' => 15, // lokasi_gedung
            'F' => 15, // lokasi_ruang
            'G' => 18, // titik_kamera
            'H' => 12, // channel_dvr
            'I' => 15, // jenis_kamera
            'J' => 15, // nama_perangkat
            'K' => 12, // merk
            'L' => 20, // teknisi_username
            'M' => 15, // tanggal_tugas
        ];
    }
}