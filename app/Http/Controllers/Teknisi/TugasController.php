<?php

namespace App\Http\Controllers\Teknisi;

class TugasController extends BaseTugasController
{
    protected string $bidang      = 'komputer';
    protected string $routePrefix = 'teknisi';

    // Tambahan method khusus komputer (kalau ada)
    // Misal: show(), hapusFoto(), dll — copy dari file lama kamu
    // yang tidak ada di BaseTugasController
}