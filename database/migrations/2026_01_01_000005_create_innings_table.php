<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->onDelete('cascade');
            $table->tinyInteger('innings_number'); // 1 or 2
            $table->foreignId('batting_team_id')->constrained('teams');
            $table->foreignId('bowling_team_id')->constrained('teams');
            $table->integer('target')->nullable();
            $table->integer('total_runs')->default(0);
            $table->integer('total_wickets')->default(0);
            $table->integer('total_balls')->default(0); // legal balls only
            $table->integer('extras')->default(0);
            $table->string('status')->default('live'); // live, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innings');
    }
};
