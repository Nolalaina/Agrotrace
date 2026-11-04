@extends('layouts.app')
@section('titre', 'Campagnes')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Campagnes</h1>
        <p class="lead">Planifiez les cycles de production et gardez une campagne active par parcelle.</p>
    </div>
    <div class="actions">
        @can('create', App\Models\Campagne::class)
            <a href="{{ route('campagnes.create') }}" class="btn btn-primary"><x-icon name="plus" /> Nouvelle campagne</a>
        @endcan
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Libellé</th>
                    <th>Parcelle</th>
                    <th>Début</th>
                    <th>Fin prévue</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($campagnes as $c)
                <tr>
                    <td><strong>{{ $c->libelle }}</strong></td>
                    <td>{{ $c->parcelle?->code }}</td>
                    <td class="nowrap">{{ $c->date_debut->format('d/m/Y') }}</td>
                    <td class="nowrap">{{ $c->date_fin_prevue->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $c->statut === 'en_cours' ? 'success' : '' }}">{{ str_replace('_', ' ', $c->statut) }}</span></td>
                    <td class="col-actions text-end">
                        <div class="row-actions">
                            @can('update', $c)
                                <a href="{{ route('campagnes.edit', $c) }}" class="btn btn-sm btn-secondary">Modifier</a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucune campagne.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $campagnes->links() }}
@endsection
