<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Player;
use App\Models\League;
use App\Models\Team;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        $league = League::factory()->create();
        $team = Team::factory()->for($league)->create();

        return [
            'name' => $this->faker->firstName() 
                     . ' ' . $this->faker->optional()->middleName()
                     . ' ' . $this->faker->lastName()
                     . ' ' . $this->faker->optional()->suffix(),
            'birthday' => $this->faker->date('Y-m-d', '2005-01-01'),
            'height' => $this->faker->numberBetween(170, 210),
            'nationality' => $this->faker->country(),
            'league_id' => $league->id,
            'team_id' => $team->id,
        ];
    }
}
