<?php

namespace App\Imports;

use App\Models\{Job, JobHistory, User};
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class JobsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithChunkReading
{
    /** @var int Jumlah baris berhasil diimport */
    public int $imported = 0;

    /** @var array Kumpulan error per baris */
    public array $errors = [];

    /** @var int|null ID admin yang melakukan import */
    protected ?int $adminId;

    /** @var string Bidang: cctv / komputer */
    protected string $bidang;

    public function __construct(?int $adminId = null, string $bidang = 'cctv')
    {
        $this->adminId = $adminId;
        $this->bidang  = $bidang;
    }

    /**
     * Mapping 1 baris Excel → 1 record Job
     *
     * 🆕 Return type `Model|array|null` WAJIB untuk maatwebsite/excel 4.x
     */
    public function model(array $row): Model|array|null
    {
        // ===== Parse tanggal =====
        $tanggalTugas = $this->parseDate($row['tanggal_tugas'] ?? null);
        if (!$tanggalTugas) {
            $this->errors[] = "Baris '{$row['judul']}' — kolom tanggal_tugas tidak valid atau kosong.";
            return null;
        }

        // ===== Cari teknisi berdasarkan username (opsional) =====
        $teknisiId = null;
        if (!empty(trim($row['teknisi_username'] ?? ''))) {
            $teknisiRole = $this->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
            $teknisi = User::where('username', trim($row['teknisi_username']))
                ->where('role', $teknisiRole)
                ->where('status', 'aktif')
                ->first();

            if (!$teknisi) {
                $this->errors[] = "Baris '{$row['judul']}' — teknisi '{$row['teknisi_username']}' tidak ditemukan (atau bukan role {$teknisiRole}).";
                return null;
            }
            $teknisiId = $teknisi->id;
        }

        // ===== Generate kode job =====
        $kodeJob = Job::generateKode($this->bidang);

        // ===== Buat Job =====
        $job = Job::create([
            'kode_job'      => $kodeJob,
            'judul'         => trim($row['judul'] ?? 'Tanpa Judul'),
            'deskripsi'     => $row['deskripsi'] ?? null,
            'bidang'        => $this->bidang,
            'kategori'      => $row['kategori'] ?? ($this->bidang === 'cctv' ? 'CCTV' : 'PC'),
            'prioritas'     => $this->normalizePrioritas($row['prioritas'] ?? 'Sedang'),

            // Lokasi & perangkat
            'lok_gedung'    => $row['lokasi_gedung'] ?? null,
            'lokasi_ruang'  => $row['lokasi_ruang'] ?? null,
            'titik_kamera'  => $row['titik_kamera'] ?? null,
            'channel_dvr'   => $row['channel_dvr'] ?? null,
            'jenis_kamera'  => $row['jenis_kamera'] ?? null,
            'computer_name' => $row['nama_perangkat'] ?? null,
            'merk'          => $row['merk'] ?? null,

            // Penugasan
            'teknisi_id'    => $teknisiId,
            'admin_id'      => $this->adminId,
            'tanggal_tugas' => $tanggalTugas->format('Y-m-d'),
            'status'        => 'DITUGASKAN',
        ]);

        // ===== Catat history =====
        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => $this->adminId,
            'status_lama' => null,
            'status_baru' => 'DITUGASKAN',
            'keterangan'  => 'Tugas dibuat via import Excel',
        ]);

        $this->imported++;
        return $job;
    }

    /**
     * Rule validasi per baris
     */
    public function rules(): array
    {
        return [
            'judul'         => 'required|string|max:200',
            'tanggal_tugas' => 'required',
        ];
    }

    /**
     * Pesan error validasi custom
     */
    public function customValidationMessages(): array
    {
        return [
            'judul.required'         => 'Kolom "judul" wajib diisi.',
            'tanggal_tugas.required' => 'Kolom "tanggal_tugas" wajib diisi.',
        ];
    }

    /**
     * Ukuran chunk biar hemat memori
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Tangkap error exception
     */
    public function onError(Throwable $e): void
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * Tangkap validation failure
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    // ============================================================
    // HELPER
    // ============================================================

    /**
     * Parse berbagai format tanggal
     */
    private function parseDate($value): ?Carbon
    {
        if (empty($value)) return null;

        // Excel serial number (mis. 45000)
        if (is_numeric($value) && $value > 1000 && $value < 100000) {
            try {
                return Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                );
            } catch (\Exception $e) {
                return null;
            }
        }

        // String tanggal
        try {
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Normalize nilai prioritas
     */
    private function normalizePrioritas(string $value): string
    {
        $value = ucfirst(strtolower(trim($value)));
        return in_array($value, ['Rendah', 'Sedang', 'Tinggi']) ? $value : 'Sedang';
    }
}