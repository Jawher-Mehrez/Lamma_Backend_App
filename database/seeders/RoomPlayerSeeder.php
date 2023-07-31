<?php

namespace Database\Seeders;

use App\Models\RoomPlayer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomPlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoomPlayer::factory()->count(10)->create();
    }
}
