<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\League;
use App\Models\Team;
use App\Models\Player;

class LeagueTeamPlayerSeeder extends Seeder
{
    private const TEAMS_PER_SUBLEAGUE = 6;
    private const ROSTER_MIN = 13;
    private const ROSTER_MAX = 15;

    public function run(): void
    {
        // Ensure the storage symlink exists
        // php artisan storage:link

        $main = League::whereIn('name', [
            'LBL&LBSL','LJBL','IZLASES','REĢIONĀLIE TURNĪRI'
        ])->get();

        $cityPool = ['Rīga','Ventspils','Liepāja','Valmiera','Ogre','Jelgava','Jūrmala','Daugavpils','Rēzekne','Cēsis','Tukums','Saldus'];
        $nickPool = ['VEF','Wolves','Titāni','Storm','Falcons','Spartans','Lions','Bulls'];

        foreach ($main as $parent) {
            // create 2–3 sub-leagues per main
            $subCount = rand(2,3);
            for ($s = 0; $s < $subCount; $s++) {
                $sub = League::firstOrCreate([
                    'name'      => $parent->name.' – '.strtoupper(Arr::random(['A','B','C','D'])).' grupa',
                    'parent_id' => $parent->id,
                ]);

                // exactly 6 teams per sub-league
                $teams = [];
                for ($t = 0; $t < self::TEAMS_PER_SUBLEAGUE; $t++) {
                    $city = Arr::random($cityPool);
                    $nick = Arr::random($nickPool);
                    $name = $city.' '.$nick;

                    // Generate a crest SVG and save it
                    [$primary, $secondary] = $this->teamColors($name);
                    $logoSvg = $this->renderTeamCrestSvg($name, $primary, $secondary);
                    $logoPath = $this->saveSvg('public', 'team-logos', Str::slug($name).'.svg', $logoSvg);

                    $teams[] = Team::create([
                        'name'      => $name,
                        'logo'      => $logoPath, // e.g. team-logos/vef-riga.svg
                        'league_id' => $sub->id,
                    ]);
                }

                // players per team (jersey SVG avatars)
                foreach ($teams as $team) {
                    $squadSize = rand(self::ROSTER_MIN, self::ROSTER_MAX);
                    $used = [];
                    // team colors derived from team name (stable)
                    [$primary, $secondary] = $this->teamColors($team->name);

                    for ($i=0; $i<$squadSize; $i++) {
                        do { $num = rand(0,99); } while (in_array($num, $used));
                        $used[] = $num;

                        $pname = fake('lv_LV')->name();
                        $jerseySvg = $this->renderJerseySvg($pname, $num, $primary, $secondary);
                        $photoPath = $this->saveSvg('public', 'players', Str::uuid().'.svg', $jerseySvg);

                        Player::create([
                            'name'          => $pname,
                            'birthday'      => fake()->dateTimeBetween('-36 years','-18 years')->format('Y-m-d'),
                            'height'        => rand(178,214),
                            'nationality'   => Arr::random(['Latvia','Latvia','Latvia','Lithuania','Estonia','Finland','Serbia','Spain','USA']),
                            'league_id'     => $team->league_id,
                            'team_id'       => $team->id,
                            'photo'         => $photoPath, // svg jersey avatar
                            'jersey_number' => $num,
                        ]);
                    }
                }
            }
        }

        // UI edge case
        $firstTeam = Team::first();
        if ($firstTeam) {
            $svg = $this->renderJerseySvg('Ēriks Āboliņš — ļoti garš vārds testiem', 77, '#ff6b6b', '#1f2937');
            $path = $this->saveSvg('public', 'players', 'edge-jersey.svg', $svg);
            Player::create([
                'name'          => 'Ēriks Āboliņš — ļoti garš vārds testiem',
                'birthday'      => '1990-01-03',
                'height'        => 190,
                'nationality'   => 'Latvia',
                'league_id'     => $firstTeam->league_id,
                'team_id'       => $firstTeam->id,
                'photo'         => $path,
                'jersey_number' => 77,
            ]);
        }
    }

    private function teamColors(string $seed): array
    {
        // Generate two consistent hex colors from a seed (team name)
        $hash = substr(sha1($seed), 0, 12);
        $r1 = hexdec(substr($hash,0,2));
        $g1 = hexdec(substr($hash,2,2));
        $b1 = hexdec(substr($hash,4,2));
        $r2 = hexdec(substr($hash,6,2));
        $g2 = hexdec(substr($hash,8,2));
        $b2 = (hexdec(substr($hash,10,2)) + 80) % 255;

        $primary   = sprintf('#%02x%02x%02x', $r1, $g1, $b1);
        $secondary = sprintf('#%02x%02x%02x', $r2, $g2, $b2);
        return [$primary, $secondary];
    }

    private function renderTeamCrestSvg(string $teamName, string $primary, string $secondary): string
    {
        $initials = strtoupper(preg_replace('/[^A-ZĀČĒĢĪĶĻŅŌŖŠŪŽ]+/u', '', iconv('UTF-8','ASCII//TRANSLIT',$teamName))) ?: strtoupper(Str::substr($teamName,0,2));
        $initials = Str::limit($initials, 3, '');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$primary}"/>
      <stop offset="100%" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <rect rx="40" ry="40" width="512" height="512" fill="url(#g)"/>
  <circle cx="256" cy="256" r="160" fill="none" stroke="#ffffff" stroke-width="12"/>
  <path d="M176,256 h160 M256,176 v160 M200,220 c60,30 108,30 168,0" stroke="#ffffff" stroke-width="10" fill="none" opacity="0.9"/>
  <text x="256" y="460" font-family="ui-sans-serif,system-ui,Segoe UI,Roboto" font-size="84" fill="#ffffff" text-anchor="middle" font-weight="700" opacity="0.95">{$initials}</text>
</svg>
SVG;
    }

    private function renderJerseySvg(string $name, int $number, string $primary, string $secondary): string
    {
        $short = Str::upper(Str::of($name)->explode(' ')->first() ?? 'PLAYER');
        $num = $number;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="640">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$primary}"/>
      <stop offset="100%" stop-color="{$secondary}"/>
    </linearGradient>
  </defs>
  <rect width="512" height="640" fill="url(#bg)"/>
  <!-- Jersey shape -->
  <path d="M120,120 h80 l20,-40 h72 l20,40 h80 v360 c0,22 -18,40 -40,40 H160 c-22,0 -40,-18 -40,-40 Z"
        fill="#ffffff" opacity="0.95" stroke="#111827" stroke-width="6"/>
  <text x="256" y="260" font-size="120" font-family="Impact,Arial Black,Arial" fill="#111827" text-anchor="middle">{$num}</text>
  <text x="256" y="320" font-size="28" font-family="ui-sans-serif,system-ui,Segoe UI,Roboto" fill="#111827" text-anchor="middle" opacity="0.85">{$short}</text>
  <!-- basketball icon -->
  <circle cx="420" cy="70" r="38" fill="none" stroke="#ffffff" stroke-width="6"/>
  <path d="M420,32 v76 M382,70 h76 M395,46 c18,18 18,46 0,64 M445,46 c-18,18 -18,46 0,64" stroke="#ffffff" stroke-width="6" fill="none"/>
</svg>
SVG;
    }

    private function saveSvg(string $disk, string $directory, string $filename, string $svg): string
    {
        $path = trim($directory,'/').'/'.$filename;
        Storage::disk($disk)->put($path, $svg);
        return $path;
    }
}
