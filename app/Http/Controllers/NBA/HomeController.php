<?php

namespace App\Http\Controllers\Nba;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home()
    {
        return view('nba.home');
    }
}
