<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Player;
use App\Models\Team;
use App\Models\League;

class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['men','women']);
        $idx = $this->faker->numberBetween(0, 99);
        $photo = "https://randomuser.me/api/portraits/{$gender}/{$idx}.jpg";

        return [
            'name' => $this->faker->name(),
            'birthday' => $this->faker->optional()->date('Y-m-d', '2005-01-01'),
            'height' => $this->faker->optional()->numberBetween(175, 215),
            'nationality' => $this->faker->randomElement(['Latvia','Latvia','Latvia','Lithuania','Estonia','Serbia','Spain','USA']),
            'team_id' => Team::factory(),
            'league_id' => League::factory(),
            'photo' => $this->faker->optional()->boolean(90) ? $photo : null,
            'jersey_number' => $this->faker->numberBetween(0, 99),
        ];
    }
}
