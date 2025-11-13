<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PerencanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ananda Firdaus II',
                'email' => 'ananda@gmail.com',
                'password' => Hash::make('password'),
                'active' => true,
                'satuan_id' => 1,
            ],
            [
                'name' => 'Rizky Maulana',
                'email' => 'rizky@gmail.com',
                'password' => Hash::make('password'),
                'active' => true,
                'satuan_id' => 2,
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            if (!$user->hasRole('Perencana')) {
                $user->syncRoles(['Perencana']);
            }
        }
    }
}
