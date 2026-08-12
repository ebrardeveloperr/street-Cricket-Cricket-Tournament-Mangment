<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** @var array<string, list<string>> */
    private array $teamPlayers = [
        'Warriors' => [
            'Ahmed', 'Ali', 'Hassan', 'Zain', 'Farhan',
            'Imran', 'Kamran', 'Nabeel', 'Rashid', 'Shoaib',
        ],
        'Titans' => [
            'Usman', 'Bilal', 'Omar', 'Tariq', 'Saad',
            'Danish', 'Faisal', 'Hamza', 'Junaid', 'Waqar',
        ],
        'Strikers' => [
            'Arslan', 'Babar', 'Fawad', 'Haris', 'Irfan',
            'Khurram', 'Moin', 'Nadeem', 'Salman', 'Yasir',
        ],
        'Knights' => [
            'Adnan', 'Asad', 'Ehsan', 'Ghulam', 'Javed',
            'Kashif', 'Latif', 'Mansoor', 'Rehan', 'Sohail',
        ],
    ];

    /** @var list<string> */
    private array $roles = ['Batter', 'Bowler', 'All-Rounder', 'Wicket-Keeper'];

    public function run(): void
    {
        Tournament::create(['name' => 'Summer Premier League 2026']);
        Tournament::create(['name' => 'Corporate Cup']);
        Tournament::create(['name' => 'Weekend Bash Series']);

        $teams = [
            Team::create(['name' => 'Warriors', 'short_name' => 'WAR']),
            Team::create(['name' => 'Titans', 'short_name' => 'TIT']),
            Team::create(['name' => 'Strikers', 'short_name' => 'STR']),
            Team::create(['name' => 'Knights', 'short_name' => 'KNI']),
        ];

        foreach ($teams as $team) {
            $names = $this->teamPlayers[$team->name];

            foreach ($names as $index => $name) {
                Player::create([
                    'team_id' => $team->id,
                    'name' => $name,
                    'role' => $this->roles[$index % count($this->roles)],
                ]);
            }
        }
    }
}
