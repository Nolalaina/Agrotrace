<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('gerer-utilisateurs');

        // User n'a pas de global scope : filtre explicite sur l'exploitation.
        return view('users.index', [
            'users' => User::where('exploitation_id', $request->user()->exploitation_id)->orderBy('name')->get(),
            'roles' => User::ROLES,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // role et exploitation_id sont fixés ici, jamais lus tels quels depuis la requête.
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'exploitation_id' => $request->user()->exploitation_id,
        ]);

        return redirect()->route('users.index')->with('succes', 'Utilisateur créé.');
    }

    public function destroy(Request $request, User $user)
    {
        Gate::authorize('gerer-utilisateurs');
        abort_unless((int) $user->exploitation_id === (int) $request->user()->exploitation_id, 404);
        abort_if($user->is($request->user()), 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        $user->delete();

        return redirect()->route('users.index')->with('succes', 'Utilisateur supprimé.');
    }
}
