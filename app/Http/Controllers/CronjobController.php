<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ApiSyncService;

class CronjobController extends Controller
{

    public function syncNbaPlayers(Request $request, ApiSyncService $service)
    {

        $token = $request->query('key');
        if ($token !== config('app.cron_token')) {
            abort(403, 'Unauthorized');
        }


        $response = Http::get('https://api.example.com/nba/players');
        $players = collect($response->json()); 

        $service->sync($players);

        return response()->json([
            'status' => 'NBA players synced successfully',
            'count' => $players->count(),
        ]);
    }
}
