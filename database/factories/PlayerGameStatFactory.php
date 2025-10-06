<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerGameStatFactory extends Factory
{
    public function definition(): array
    {

        $fga2 = $this->faker->numberBetween(0, 10);
        $fgm2 = $this->faker->numberBetween(0, $fga2);

        $fga3 = $this->faker->numberBetween(0, 8);
        $fgm3 = $this->faker->numberBetween(0, $fga3);

        $fta  = $this->faker->numberBetween(0, 6);
        $ftm  = $this->faker->numberBetween(0, $fta);


        $points = $fgm2 * 2 + $fgm3 * 3 + $ftm;


        $oreb = $this->faker->numberBetween(0, 5);
        $dreb = $this->faker->numberBetween(0, 8);
        $reb = $oreb + $dreb;


        $ast = $this->faker->numberBetween(0, 10);
        $tov = $this->faker->numberBetween(0, 6);
        $stl = $this->faker->numberBetween(0, 4);
        $blk = $this->faker->numberBetween(0, 3);
        $pf  = $this->faker->numberBetween(0, 5);

        $eff = $points + $reb + $ast + $stl + $blk - ($fga2 - $fgm2) - ($fga3 - $fgm3) - ($fta - $ftm) - $tov;

        return [
            'game_id' => Game::factory(),
            'player_id' => Player::factory(),
            'team_id' => Team::factory(),

            'minutes' => $this->faker->numberBetween(5, 40) . ':' . str_pad($this->faker->numberBetween(0, 59), 2, '0', STR_PAD_LEFT),

            'points' => $points,

            'fgm2' => $fgm2,
            'fga2' => $fga2,

            'fgm3' => $fgm3,
            'fga3' => $fga3,

            'ftm' => $ftm,
            'fta' => $fta,

            'oreb' => $oreb,
            'dreb' => $dreb,
            'reb' => $reb,

            'ast' => $ast,
            'tov' => $tov,
            'stl' => $stl,
            'blk' => $blk,
            'pf'  => $pf,

            'eff' => $eff,
            'plus_minus' => $this->faker->numberBetween(-15, 15),

            'status' => $this->faker->randomElement(['played', 'dnp']),
        ];
    }
}
