<?php

namespace App\Jobs;

use App\Models\NbaPlayerDetail;
use App\Services\NbaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncPlayerDetailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $playerId;

    public function __construct(int $playerId)
    {
        $this->playerId = $playerId;
    }

    public function handle(NbaService $nbaService)
    {

        set_time_limit(0); // prevent PHP timeout for this job

        $athlete = $nbaService->playerInfo($this->playerId);

        if (empty($athlete)) {
            return;
        }

        NbaPlayerDetail::updateOrCreate(
            ['external_id' => $athlete['id'] ?? null],
            [
                'uid'                 => $athlete['uid'] ?? null,
                'guid'                => $athlete['guid'] ?? null,
                'first_name'          => $athlete['firstName'] ?? null,
                'last_name'           => $athlete['lastName'] ?? null,
                'full_name'           => $athlete['fullName'] ?? null,
                'display_name'        => $athlete['displayName'] ?? null,
                'jersey'              => $athlete['jersey'] ?? null,
                'links'               => isset($athlete['links']) ? json_encode($athlete['links']) : null,
                'college'             => isset($athlete['college']) ? json_encode($athlete['college']) : null,
                'college_team'        => isset($athlete['collegeTeam']) ? json_encode($athlete['collegeTeam']) : null,
                'college_athlete'     => isset($athlete['collegeAthlete']) ? json_encode($athlete['collegeAthlete']) : null,
                'headshot_href'       => $athlete['headshot']['href'] ?? null,
                'headshot_alt'        => $athlete['headshot']['alt'] ?? null,
                'position'            => isset($athlete['position']) ? json_encode($athlete['position']) : null,
                'team'                => isset($athlete['team']) ? json_encode($athlete['team']) : null,
                'active'              => $athlete['active'] ?? null,
                'status'              => isset($athlete['status']) ? json_encode($athlete['status']) : null,
                'birth_place'         => $athlete['displayBirthPlace'] ?? null,
                'display_height'      => $athlete['displayHeight'] ?? null,
                'display_weight'      => $athlete['displayWeight'] ?? null,
                'display_dob'         => $athlete['displayDOB'] ?? null,
                'age'                 => $athlete['age'] ?? null,
                'display_jersey'      => $athlete['displayJersey'] ?? null,
                'display_experience'  => $athlete['displayExperience'] ?? null,
                'display_draft'       => $athlete['displayDraft'] ?? null,
            ]
        );
    }
}
