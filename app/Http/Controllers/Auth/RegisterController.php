<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Exploitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'exploitation_nom' => ['required', 'string', 'max:255'],
            'localisation' => ['required', 'string', 'max:255'],
        ]);

        $exploitation = Exploitation::create([
            'nom' => $validated['exploitation_nom'],
            'localisation' => $validated['localisation'],
            'type' => 'production_fruitiere',
            'superficie_ha' => 0,
            'statut' => 'active',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_ADMIN,
            'exploitation_id' => $exploitation->id,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
