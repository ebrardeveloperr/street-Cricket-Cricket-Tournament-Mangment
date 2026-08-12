<?php

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use Database\Seeders\DatabaseSeeder;

test('seeder creates ten players for each team', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Tournament::count())->toBe(3)
        ->and(Team::count())->toBe(4)
        ->and(Player::count())->toBe(40);

    Team::all()->each(function (Team $team) {
        expect($team->players()->count())->toBe(10);
    });
});

test('scoring page includes player select dropdowns', function () {
    $this->seed(DatabaseSeeder::class);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('id="striker-select"', false)
        ->assertSee('id="nonstriker-select"', false)
        ->assertSee('id="opening-bowler-select"', false)
        ->assertSee('id="second-innings-banner"', false)
        ->assertSee('id="setup-title"', false)
        ->assertSee('id="modal-innings-target"', false)
        ->assertSee('id="modal-match-result"', false);
});

test('match can be created when teams and players exist', function () {
    $this->seed(DatabaseSeeder::class);

    $battingTeam = Team::where('name', 'Warriors')->firstOrFail();
    $bowlingTeam = Team::where('name', 'Titans')->firstOrFail();
    $tournament = Tournament::firstOrFail();

    $this->postJson('/api/matches', [
        'tournament_id' => $tournament->id,
        'team1_id' => $battingTeam->id,
        'team2_id' => $bowlingTeam->id,
        'total_overs' => 20,
    ])->assertCreated()
        ->assertJsonStructure(['match_id', 'inning_id']);
});
