<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    User::create([
        'name' => 'Admin Utama',
        'email' => 'admin@mail.com',
        'password' => Hash::make('123456'),
        'role' => 'admin',
    ]);

    User::create([
        'name' => 'Sales 1',
        'email' => 'sales@mail.com',
        'password' => Hash::make('123456'),
        'role' => 'sales',
    ]);

    User::create([
        'name' => 'Teknisi 1',
        'email' => 'teknisi@mail.com',
        'password' => Hash::make('123456'),
        'role' => 'teknisi',
    ]);
}
}
