<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeknisiCctvSeeder extends Seeder
{
    public function run(): void
    {
        // Update role teknisi yang sudah ada jadi lebih jelas + tambah teknisi_cctv baru
        User::updateOrCreate(
            ['username' => 'teknisi_cctv'],
            [
                'nama'     => 'Teknisi CCTV',
                'email'    => 'cctv@example.com',
                'password' => Hash::make('cctv123'),
                'role'     => 'teknisi_cctv',
                'status'   => 'aktif',
            ]
        );
    }
}