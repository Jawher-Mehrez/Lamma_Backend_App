<?php

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('room_players', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Room::class)->references('id')->on('rooms')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('score')->default(0);
            $table->unsignedTinyInteger('rank')->default(0);
            $table->unsignedTinyInteger('kicked')->default(0);
            $table->unsignedTinyInteger('left')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_players');
    }
};
