<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Player;
use App\Models\Team;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'birthday' => $this->faker->optional()->date('Y-m-d', '2005-01-01'),
            'height' => $this->faker->optional()->numberBetween(170, 210),
            'nationality' => $this->faker->country(),
            'team_id' => null, 
            'league_id' => null, 
            'photo' => $this->faker->optional()->imageUrl(200, 200, 'sports'),
            'jersey_number' => $this->faker->optional()->numberBetween(0, 99),
        ];
    }
}
