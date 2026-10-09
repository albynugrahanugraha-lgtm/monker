<?php

namespace App\Http\Controllers\Teknisi;

/**
 * TugasController — untuk teknisi KOMPUTER
 *
 * Semua logic ada di BaseTugasController.
 * Class ini cuma nyetel:
 *   - bidang      = 'komputer'
 *   - routePrefix = 'teknisi'
 *
 * Otomatis:
 *   - Filter job hanya bidang='komputer'
 *   - Kode job prefix "PC-YYYYMMDD-XXX"
 *   - Route: teknisi.dashboard, teknisi.buat, teknisi.kerjakan, dll
 */
class TugasController extends BaseTugasController
{
    /** Bidang yang ditangani: 'komputer' */
    protected string $bidang = 'komputer';

    /** Prefix nama route: 'teknisi' → route('teknisi.dashboard'), dll */
    protected string $routePrefix = 'teknisi';
}