<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Technician;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // SUPERADMIN
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nama'     => 'Super Admin',
                'email'    => 'superadmin@example.com',
                'password' => Hash::make('super123'),
                'role'     => 'superadmin',
                'no_hp'    => '0811111111',
                'status'   => 'aktif',
            ]
        );

        // ADMIN
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama'     => 'Admin Satu',
                'email'    => 'admin@example.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
                'no_hp'    => '0822222222',
                'status'   => 'aktif',
            ]
        );

        // TEKNISI
        $teknisi = User::updateOrCreate(
            ['username' => 'teknisi'],
            [
                'nama'     => 'Budi Teknisi',
                'email'    => 'budi@example.com',
                'password' => Hash::make('teknisi123'),
                'role'     => 'teknisi',
                'no_hp'    => '0833333333',
                'status'   => 'aktif',
            ]
        );

        Technician::updateOrCreate(
            ['user_id' => $teknisi->id],
            ['no_hp' => '0833333333', 'status' => 'aktif']
        );
    }
}