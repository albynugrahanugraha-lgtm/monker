<?php

namespace App\Exports;

use App\Models\Job;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanExport implements FromArray, WithEvents, WithColumnWidths
{
    protected $jobs;
    protected $stats;
    protected $rekapTeknisi;
    protected $chartTahunan;
    protected $tahun;
    protected $bulan;
    protected $bidang;
    protected $user;
    protected $daftarBulan;

    protected $lastRekapBulanRow = 0;
    protected $lastRekapTeknisiRow = 0;

    public function __construct($jobs, $stats, $rekapTeknisi, $chartTahunan, $tahun, $bulan, $bidang, $user, $daftarBulan)
    {
        $this->jobs         = $jobs;
        $this->stats        = $stats;
        $this->rekapTeknisi = $rekapTeknisi;
        $this->chartTahunan = $chartTahunan;
        $this->tahun        = $tahun;
        $this->bulan        = $bulan;
        $this->bidang       = $bidang;
        $this->user         = $user;
        $this->daftarBulan  = $daftarBulan;
    }

    /**
     * Data array kosong — semua data ditulis manual via registerEvents()
     */
    public function array(): array
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 18,
            'C' => 12,
            'D' => 12,
            'E' => 30,
            'F' => 20,
            'G' => 15,
            'H' => 15,
            'I' => 20,
            'J' => 15,
            'K' => 10,
            'L' => 12,
            'M' => 25,
            'N' => 25,
            'O' => 25,   // Foto Sebelum — lebih lebar
            'P' => 25,   // Foto Sesudah — lebih lebar
            'Q' => 12,
            'R' => 12,
            'S' => 12,
            'T' => 12,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $this->buildHeader($sheet);
                $this->buildRekapBulanan($sheet);

                if (!$this->user->isAnyTeknisi() && $this->rekapTeknisi->count() > 0) {
                    $this->buildRekapTeknisi($sheet);
                }

                $this->buildDetailPekerjaan($sheet, $event);

                $sheet->getSheetView()->setZoomScale(80);
            },
        ];
    }

    /**
     * HEADER UTAMA
     */
    private function buildHeader($sheet)
    {
        $row = 1;

        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->setCellValue("A{$row}", 'LAPORAN PEKERJAAN TEKNISI');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 18, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(35);
        $row++;

        $periode = '';
        if ($this->bulan && $this->bulan !== 'semua') {
            $periode = ($this->daftarBulan[$this->bulan] ?? '') . ' ' . $this->tahun;
        } else {
            $periode = 'Tahun ' . $this->tahun . ' (Januari - Desember)';
        }

        if ($this->user->isAnyTeknisi()) {
            $periode .= ' — Teknisi: ' . $this->user->display_name;
        } elseif ($this->bidang) {
            $periode .= ' — Bidang: ' . ($this->bidang === 'cctv' ? 'CCTV' : 'Komputer');
        }

        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->setCellValue("A{$row}", 'Periode: ' . $periode);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['size' => 12],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F0FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    /**
     * SECTION A: REKAP PER BULAN
     */
    private function buildRekapBulanan($sheet)
    {
        $row = 4;

        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->setCellValue("A{$row}", 'A. REKAP PER BULAN — ' . $this->tahun);
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
        ]);
        $row++;

        // Table header
        $sheet->setCellValue("A{$row}", 'Keterangan');
        $col = 'B';
        foreach ($this->chartTahunan['labels'] as $label) {
            $sheet->setCellValue("{$col}{$row}", $label);
            $col++;
        }
        $sheet->setCellValue("{$col}{$row}", 'TOTAL');

        $headerRange = "A{$row}:{$col}{$row}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E0FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        // Total Pekerjaan
        $sheet->setCellValue("A{$row}", 'Total Pekerjaan');
        $col = 'B';
        foreach ($this->chartTahunan['data'] as $val) {
            $sheet->setCellValue("{$col}{$row}", $val);
            $col++;
        }
        $sheet->setCellValue("{$col}{$row}", array_sum($this->chartTahunan['data']));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $row++;

        // Selesai
        $sheet->setCellValue("A{$row}", 'Selesai');
        $col = 'B';
        foreach ($this->chartTahunan['selesai'] as $val) {
            $sheet->setCellValue("{$col}{$row}", $val);
            $col++;
        }
        $sheet->setCellValue("{$col}{$row}", array_sum($this->chartTahunan['selesai']));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $row++;

        // Pending
        $sheet->setCellValue("A{$row}", 'Pending');
        $col = 'B';
        foreach ($this->chartTahunan['data'] as $i => $val) {
            $pending = $val - ($this->chartTahunan['selesai'][$i] ?? 0);
            $sheet->setCellValue("{$col}{$row}", $pending);
            $col++;
        }
        $totalPending = array_sum($this->chartTahunan['data']) - array_sum($this->chartTahunan['selesai']);
        $sheet->setCellValue("{$col}{$row}", $totalPending);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $this->lastRekapBulanRow = $row;
    }

    /**
     * SECTION B: REKAP PER TEKNISI
     */
    private function buildRekapTeknisi($sheet)
    {
        $row = $this->lastRekapBulanRow + 2;

        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->setCellValue("A{$row}", 'B. REKAP PER TEKNISI');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
        ]);
        $row++;

        // Table header
        $headers = ['No', 'Nama Teknisi', 'Total', 'Selesai', 'Pending', 'Rasio'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$row}", $h);
            $col++;
        }

        // Kolom bulan
        foreach ($this->chartTahunan['labels'] as $label) {
            $sheet->setCellValue("{$col}{$row}", $label);
            $col++;
        }

        $lastCol = chr(ord($col) - 1);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E0FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $row++;

        // Isi data
        foreach ($this->rekapTeknisi as $i => $r) {
            $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $r->teknisi->display_name ?? '-');
            $sheet->setCellValue("C{$row}", $r->total);
            $sheet->setCellValue("D{$row}", $r->selesai);
            $sheet->setCellValue("E{$row}", $r->pending);
            $sheet->setCellValue("F{$row}", $rasio . '%');

            // Kolom bulan
            $col = 'G';
            for ($m = 1; $m <= 12; $m++) {
                $count = Job::where('teknisi_id', $r->teknisi_id)
                    ->whereMonth('tanggal_tugas', $m)
                    ->whereYear('tanggal_tugas', $this->tahun)
                    ->where('status', 'SELESAI')
                    ->count();

                if ($this->bidang && in_array($this->bidang, ['komputer', 'cctv'])) {
                    $count = Job::where('teknisi_id', $r->teknisi_id)
                        ->where('bidang', $this->bidang)
                        ->whereMonth('tanggal_tugas', $m)
                        ->whereYear('tanggal_tugas', $this->tahun)
                        ->where('status', 'SELESAI')
                        ->count();
                }

                $sheet->setCellValue("{$col}{$row}", $count > 0 ? $count : '-');
                $col++;
            }

            $lastCol = chr(ord($col) - 1);
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $row++;
        }

        // TOTAL
        $totalAll   = $this->rekapTeknisi->sum('total');
        $selesaiAll = $this->rekapTeknisi->sum('selesai');
        $pendingAll = $this->rekapTeknisi->sum('pending');
        $rasioAll   = $totalAll > 0 ? round(($selesaiAll / $totalAll) * 100) : 0;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("C{$row}", $totalAll);
        $sheet->setCellValue("D{$row}", $selesaiAll);
        $sheet->setCellValue("E{$row}", $pendingAll);
        $sheet->setCellValue("F{$row}", $rasioAll . '%');

        $col = 'G';
        foreach ($this->chartTahunan['selesai'] as $val) {
            $sheet->setCellValue("{$col}{$row}", $val);
            $col++;
        }

        $lastCol = chr(ord($col) - 1);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F0FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $this->lastRekapTeknisiRow = $row;
    }

    /**
     * SECTION C: DETAIL PEKERJAAN — DENGAN GAMBAR ASLI
     */
    private function buildDetailPekerjaan($sheet, AfterSheet $event)
    {
        $row = $this->lastRekapTeknisiRow > 0
            ? $this->lastRekapTeknisiRow + 2
            : $this->lastRekapBulanRow + 2;

        $sheet->mergeCells("A{$row}:T{$row}");
        $sheet->setCellValue("A{$row}", 'C. DETAIL PEKERJAAN (' . $this->jobs->count() . ' pekerjaan)');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4A90E2']],
        ]);
        $row++;

        // Header kolom
        $headers = [
            'No', 'Kode', 'Bidang', 'Tanggal', 'Judul',
            'Teknisi', 'Gedung', 'Ruang', 'Perangkat', 'Kategori',
            'Prioritas', 'Status', 'Hasil', 'Catatan',
            '📷 Foto Sebelum', '📷 Foto Sesudah',
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}{$row}", $h);
            $col++;
        }

        $lastCol = chr(ord($col) - 1);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D0E0FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(28);
        $row++;

        $worksheet = $event->sheet->getDelegate();

        // Isi data
        foreach ($this->jobs as $i => $j) {
            // Set nilai kolom text (kolom O & P dibiarkan kosong, isinya gambar)
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $j->kode_job);
            $sheet->setCellValue("C{$row}", $j->bidangLabel());
            $sheet->setCellValue("D{$row}", $j->tanggal_tugas?->format('d-m-Y') ?? '-');
            $sheet->setCellValue("E{$row}", $j->judul);
            $sheet->setCellValue("F{$row}", $j->teknisi->display_name ?? '-');
            $sheet->setCellValue("G{$row}", $j->lok_gedung ?? '-');
            $sheet->setCellValue("H{$row}", $j->lokasi_ruang ?? '-');
            $sheet->setCellValue("I{$row}", ($j->computer_name ?? '-') . ' (' . ($j->merk ?? '-') . ')');
            $sheet->setCellValue("J{$row}", $j->kategori ?? '-');
            $sheet->setCellValue("K{$row}", $j->prioritas);
            $sheet->setCellValue("L{$row}", $j->status);
            $sheet->setCellValue("M{$row}", $j->hasil_pekerjaan ?? '-');
            $sheet->setCellValue("N{$row}", $j->catatan ?? '-');
            // O & P dikosongkan — akan diisi gambar

            // Style baris
            $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Warna status
            $statusColor = match ($j->status) {
                'SELESAI'    => 'C6EFCE',
                'DIKERJAKAN' => 'FFEB9C',
                default      => 'FFC7CE',
            };
            $sheet->getStyle("L{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($statusColor);

            // ============================================
            // 🆕 EMBED FOTO SEBELUM — gambar asli di kolom O
            // ============================================
            $rowHeightSebelum = 60;
            foreach ($j->fotoSebelum->take(2) as $idx => $f) {
                $path = $this->resolveImagePath($f->nama_file);

                if ($path && file_exists($path)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Sebelum ' . ($idx + 1));
                    $drawing->setDescription('Foto Sebelum ' . ($idx + 1));
                    $drawing->setPath($path);
                    $drawing->setHeight(55); // tinggi gambar dalam pixel
                    $drawing->setCoordinates("O{$row}");
                    $drawing->setOffsetX(5 + ($idx * 60)); // jarak antar gambar dalam pixel
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($worksheet);
                }
            }

            // ============================================
            // 🆕 EMBED FOTO SESUDAH — gambar asli di kolom P
            // ============================================
            foreach ($j->fotoSesudah->take(2) as $idx => $f) {
                $path = $this->resolveImagePath($f->nama_file);

                if ($path && file_exists($path)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Sesudah ' . ($idx + 1));
                    $drawing->setDescription('Foto Sesudah ' . ($idx + 1));
                    $drawing->setPath($path);
                    $drawing->setHeight(55);
                    $drawing->setCoordinates("P{$row}");
                    $drawing->setOffsetX(5 + ($idx * 60));
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($worksheet);
                }
            }

            // Set tinggi baris untuk muat gambar
            $sheet->getRowDimension($row)->setRowHeight(70);

            $row++;
        }

        // Kalau tidak ada data
        if ($this->jobs->count() === 0) {
            $sheet->mergeCells("A{$row}:P{$row}");
            $sheet->setCellValue("A{$row}", 'Tidak ada data');
            $sheet->getStyle("A{$row}")->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }

        // Footer
        $row += 2;
        $sheet->mergeCells("A{$row}:P{$row}");
        $sheet->setCellValue("A{$row}", 'Dicetak pada: ' . now()->format('d-m-Y H:i'));
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    /**
     * Cari path file gambar — coba beberapa kemungkinan lokasi
     */
    private function resolveImagePath(string $namaFile): ?string
    {
        // Hapus leading slash kalau ada
        $namaFile = ltrim($namaFile, '/');

        $candidates = [
            public_path($namaFile),                          // public/uploads/jobs/xxx.jpg
            base_path($namaFile),                            // root project/uploads/jobs/xxx.jpg
            storage_path('app/public/' . $namaFile),         // storage/app/public/uploads/jobs/xxx.jpg
            public_path('storage/' . $namaFile),             // public/storage/uploads/jobs/xxx.jpg
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                return $path;
            }
        }

        return null;
    }
}