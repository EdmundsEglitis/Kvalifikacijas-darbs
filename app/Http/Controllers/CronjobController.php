<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\ApiSyncService;

class CronjobController extends Controller
{
    /**
     * Sync NBA players from the API.
     */
    public function syncNbaPlayers(Request $request, ApiSyncService $service)
    {
        // Secure the route with a token
        $token = $request->query('key');
        if ($token !== config('app.cron_token')) {
            abort(403, 'Unauthorized');
        }

        // Fetch the API data
        $response = Http::get('https://api.example.com/nba/players');
        $players = collect($response->json()); // make sure it's a collection

        // Sync with the database
        $service->sync($players);

        return response()->json([
            'status' => 'NBA players synced successfully',
            'count' => $players->count(),
        ]);
    }
}
