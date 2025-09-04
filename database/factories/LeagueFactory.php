<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\League;

class LeagueFactory extends Factory
{
    protected $model = League::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(), // generates a random league name
        ];
    }
}
