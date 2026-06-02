<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Amministratore',
                'email' => 'admin@example.com',
                'password' => Hash::make('secret')
            ],
            [
                'name' => 'Utente',
                'email' => 'user@example.com',
                'password' => Hash::make('secret')
            ],
            [
                'name' => 'Ospite',
                'email' => 'ospite@example.com',
                'password' => Hash::make('secret')
            ]
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
