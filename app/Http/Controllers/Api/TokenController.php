<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            Log::warning('API : échec d\'authentification', ['email' => $data['email'], 'ip' => $request->ip()]);

            return response()->json(['message' => 'Identifiants invalides.'], 401);
        }

        return response()->json([
            'token' => $user->createToken($data['device'] ?? 'api')->plainTextToken,
            'token_type' => 'Bearer',
        ], 201);
    }
}
