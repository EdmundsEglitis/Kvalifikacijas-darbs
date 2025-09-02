<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NbaController extends Controller
{
    public function status()
    {
        // Simple call to NBA API status endpoint
        $response = Http::withHeaders(config('nba.headers.api-sports'))
            ->get(config('nba.url') . '/status');

        $data = $response->json();

        return view('nba.status', compact('data'));
    }
}
