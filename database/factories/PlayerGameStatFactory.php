<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Player;
use App\Models\Team;
use App\Models\Game;
use App\Models\PlayerGameStat;

class PlayerGameStatFactory extends Factory
{
    protected $model = PlayerGameStat::class;

    public function definition(): array
    {
        $player = Player::inRandomOrder()->first();
        $team = $player?->team;
        $game = Game::inRandomOrder()->first();

        return [
            'player_id' => $player?->id,
            'team_id' => $team?->id,
            'game_id' => $game?->id,
            'points' => $this->faker->numberBetween(0, 30),
            'reb' => $this->faker->numberBetween(0, 15),
            'ast' => $this->faker->numberBetween(0, 12),
            'stl' => $this->faker->numberBetween(0, 5),
            'blk' => $this->faker->numberBetween(0, 5),
            'eff' => $this->faker->numberBetween(-5, 35),
        ];
    }
}
