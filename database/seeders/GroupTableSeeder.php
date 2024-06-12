<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::truncate();
        UserGroup::truncate();

        Group::create(['name' => 'root']);
        Group::create(['name' => 'admin']);
        Group::create(['name' => 'premium']);

        UserGroup::create(['user_id' => 1, 'group_id' => 1]);
    }
}
