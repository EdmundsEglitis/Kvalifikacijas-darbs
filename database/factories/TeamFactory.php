<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Team;
use App\Models\League;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $city = $this->faker->randomElement(['Rīga','Ventspils','Liepāja','Valmiera','Ogre','Jelgava']);
        $nick = $this->faker->randomElement(['VEF','Wolves','Titāni','Storm','Falcons','Spartans']);
        $name = "$city $nick";
        $logo = 'https://api.dicebear.com/7.x/shapes/svg?seed=' . urlencode($name) . '&size=200';

        return [
            'name' => $name,
            'logo' => $logo,
            'league_id' => League::factory(),
        ];
    }
}
