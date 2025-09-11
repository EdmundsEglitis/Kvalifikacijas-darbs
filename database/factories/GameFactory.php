<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Game;
use App\Models\Team;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        $team1 = Team::inRandomOrder()->first();
        $team2 = Team::where('id', '!=', $team1->id)->inRandomOrder()->first();

        $team1Score = $this->faker->numberBetween(50, 120);
        $team2Score = $this->faker->numberBetween(50, 120);

        return [
            'team1_id' => $team1?->id,
            'team2_id' => $team2?->id,
            'score' => $team1Score . ':' . $team2Score,
            'winner_id' => $team1Score > $team2Score ? $team1?->id : $team2?->id,
            'date' => $this->faker->dateTimeBetween('-2 months', '+2 months'),
        ];
    }
}
