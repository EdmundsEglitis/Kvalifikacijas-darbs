<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- add this
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory; // <-- add this
    use Notifiable;

    // Your existing code...

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->admin; // admin column in your migration
    }
}
