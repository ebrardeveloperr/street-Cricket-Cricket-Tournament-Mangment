<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inning_id')->constrained('innings')->onDelete('cascade');
            $table->integer('over_number');
            $table->integer('ball_number'); // 1-6 (legal balls within the over)
            $table->foreignId('batsman_id')->constrained('players');
            $table->foreignId('non_striker_id')->constrained('players');
            $table->foreignId('bowler_id')->constrained('players');
            $table->integer('runs')->default(0); // runs off the bat
            $table->string('extra_type')->nullable(); // WD, NB, B, LB
            $table->integer('extra_runs')->default(0);
            $table->boolean('is_wicket')->default(false);
            $table->string('wicket_type')->nullable(); // Bowled, Caught, RO, LBW, etc.
            $table->foreignId('player_out_id')->nullable()->constrained('players');
            $table->boolean('is_legal')->default(true); // false for WD/NB
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
