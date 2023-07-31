<?php

use App\Models\Category;
use App\Models\Location;
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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Category::class)->references('id')->on('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Location::class)->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('max_players')->default(4);
            $table->enum('status', ['active', 'deactivated', 'closed'])->default('deactivated');
            $table->decimal('winners_prize', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
