<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::truncate();
        User::create([
            'email' => 'admin@kelio.net',
            'first_name'=>'Kelio',
            'last_name'=>'Okeli',
            'activated'=> 1,
            'password' => bcrypt('changeme'),
        ]);
    }
}
