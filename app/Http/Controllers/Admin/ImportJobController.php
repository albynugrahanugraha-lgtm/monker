<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\JobsImport;
use App\Exports\JobsTemplateExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ImportJobController extends Controller
{
    /**
     * Halaman form import
     */
    public function index()
    {
        $teknisiCctv = User::where('role', 'teknisi_cctv')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $teknisiKomputer = User::where('role', 'teknisi')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.import_job', compact('teknisiCctv', 'teknisiKomputer'));
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate(Request $request)
    {
        $bidang = $request->input('bidang', 'cctv');
        if (!in_array($bidang, ['komputer', 'cctv'])) {
            $bidang = 'cctv';
        }

        $filename = 'template-import-jobs-' . $bidang . '-' . date('Ymd') . '.xlsx';

        return Excel::download(new JobsTemplateExport($bidang), $filename);
    }

    /**
     * Proses upload & import
     */
    public function store(Request $request)
    {
        // ===== Validasi =====
        $request->validate([
            'file'   => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'bidang' => 'required|in:komputer,cctv',
        ], [
            'file.required'   => 'File Excel wajib dipilih.',
            'file.mimes'      => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max'        => 'Ukuran file maksimal 10MB.',
            'bidang.required' => 'Bidang wajib dipilih.',
            'bidang.in'       => 'Bidang harus komputer atau cctv.',
        ]);

        $fileUpload = $request->file('file');

        if (!$fileUpload || !$fileUpload->isValid()) {
            return redirect()->route('admin.import.index')
                ->with('error', '❌ File upload tidak valid atau gagal di-upload.');
        }

        // ===== Simpan file ke storage sementara =====
        $importDir = storage_path('app/imports');
        if (!File::isDirectory($importDir)) {
            File::makeDirectory($importDir, 0755, true);
        }

        $extension = $fileUpload->getClientOriginalExtension() ?: 'xlsx';
        $filename  = 'import-' . date('YmdHis') . '-' . uniqid() . '.' . $extension;

        try {
            $fileUpload->move($importDir, $filename);
        } catch (\Exception $e) {
            return redirect()->route('admin.import.index')
                ->with('error', '❌ Gagal menyimpan file: ' . $e->getMessage());
        }

        $fullPath = $importDir . DIRECTORY_SEPARATOR . $filename;

        // Cek file benar-benar ada & bisa dibaca
        if (!File::exists($fullPath) || !is_readable($fullPath)) {
            return redirect()->route('admin.import.index')
                ->with('error', '❌ File tidak ditemukan atau tidak bisa dibaca.');
        }

        try {
            // ===== Jalankan Import =====
            $import = new JobsImport(auth()->id(), $request->bidang);

            // Pakai path file (bukan UploadedFile object)
            Excel::import($import, $fullPath);

            $imported = $import->imported;
            $errors   = $import->errors;

            // ===== Hapus file setelah selesai =====
            @unlink($fullPath);

            // ===== Response =====
            if ($imported > 0 && empty($errors)) {
                return redirect()->route('admin.import.index')
                    ->with('success', "✅ Berhasil import {$imported} pekerjaan {$request->bidang}.");
            }

            if ($imported > 0 && !empty($errors)) {
                return redirect()->route('admin.import.index')
                    ->with('warning', "⚠️ Berhasil import {$imported} pekerjaan, tapi ada " . count($errors) . " baris gagal.")
                    ->with('import_errors', $errors);
            }

            return redirect()->route('admin.import.index')
                ->with('error', '❌ Tidak ada data yang berhasil diimport.')
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            @unlink($fullPath);

            return redirect()->route('admin.import.index')
                ->with('error', '❌ Gagal import: ' . $e->getMessage());
        }
    }
}