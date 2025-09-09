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
            'name' => $this->faker->unique()->word(),
            'parent_id' => null, // default to main league
        ];
    }

    /** Set this league as a sub-league of a parent */
    public function subLeague(League $parent)
    {
        return $this->state(fn () => ['parent_id' => $parent->id]);
    }
}
