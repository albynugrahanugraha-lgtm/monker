<?php

namespace App\Http\Controllers\Teknisi;

/**
 * TugasCctvController — untuk teknisi CCTV
 *
 * Semua logic ada di BaseTugasController.
 * Class ini cuma nyetel:
 *   - bidang      = 'cctv'
 *   - routePrefix = 'teknisi_cctv'
 *
 * Otomatis:
 *   - Filter job hanya bidang='cctv'
 *   - Kode job prefix "CCTV-YYYYMMDD-XXX"
 *   - Route: teknisi_cctv.dashboard, teknisi_cctv.buat, dll
 */
class TugasCctvController extends BaseTugasController
{
    /** Bidang yang ditangani: 'cctv' */
    protected string $bidang = 'cctv';

    /** Prefix nama route */
    protected string $routePrefix = 'teknisi_cctv';
}