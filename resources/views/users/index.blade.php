@extends('layouts.app')
@section('titre', 'Utilisateurs')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Utilisateurs de l'exploitation</h1>
        <p class="lead">Gérez les rôles et les accès de l'équipe.</p>
    </div>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td><strong>{{ $u->name }}</strong> @if ($u->id === auth()->id()) <span class="muted">(vous)</span> @endif</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge {{ $u->role === 'admin' ? 'info' : '' }}">{{ $u->role }}</span></td>
                    <td class="col-actions text-end">
                        <div class="row-actions">
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}" data-confirm="Supprimer l'utilisateur {{ $u->name }} ?" data-confirm-label="Supprimer">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-head"><h3>Nouvel utilisateur</h3></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field span-3">
                    <label class="label" for="name">Nom</label>
                    <input id="name" name="name" class="input" value="{{ old('name') }}" required>
                </div>
                <div class="field span-4">
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="input" value="{{ old('email') }}" required>
                </div>
                <div class="field span-2">
                    <label class="label" for="role">Rôle</label>
                    <select id="role" name="role" class="select">
                        @foreach ($roles as $r)
                            <option value="{{ $r }}" @selected(old('role') === $r)>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field span-3">
                    <label class="label" for="password">Mot de passe</label>
                    <input id="password" type="password" name="password" class="input" autocomplete="new-password" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><x-icon name="plus" /> Créer l'utilisateur</button>
            </div>
        </form>
    </div>
</section>
@endsection
