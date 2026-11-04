@extends('layouts.app')
@section('titre', 'Stocks et intrants')
@section('contenu')
<div class="page-head">
    <div>
        <h1>Intrants et mouvements</h1>
        <p class="lead">Suivez les entrées, consommations et ajustements de stock sans jamais passer sous le seuil.</p>
    </div>
</div>

<section class="panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Intrant</th>
                    <th>Catégorie</th>
                    <th class="text-end">Disponible</th>
                    <th class="text-end">Seuil</th>
                    <th>Nouveau mouvement</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($intrants as $i)
                <tr class="{{ $i->estCritique() ? 'is-danger' : '' }}">
                    <td>
                        <strong>{{ $i->nom }}</strong>
                        @if ($i->estCritique()) <span class="badge danger">Stock critique</span> @endif
                    </td>
                    <td>{{ $i->categorie }}</td>
                    <td class="text-end nowrap">{{ $i->quantite_disponible }} {{ $i->unite }}</td>
                    <td class="text-end">{{ $i->seuil_minimum }}</td>
                    <td>
                        <form method="POST" action="{{ route('intrants.mouvement', $i) }}" class="inline-form" data-movement-form>
                            @csrf
                            <select name="type" class="select select-sm" style="width:140px" aria-label="Type de mouvement">
                                @foreach ($types as $t)
                                    <option value="{{ $t }}" @selected($t === 'entree')>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                                @endforeach
                            </select>
                            <input type="number" step="0.01" name="quantite" class="input input-sm" style="width:100px" placeholder="Qté ({{ $i->unite }})" aria-label="Quantité" required>
                            <input type="number" step="0.01" min="0" name="cout_total" class="input input-sm" style="width:120px" placeholder="Coût total" aria-label="Coût total">
                            <select name="campagne_id" class="select select-sm" style="width:160px" aria-label="Campagne">
                                <option value="">Campagne…</option>
                                @foreach ($campagnes as $c)
                                    <option value="{{ $c->id }}">{{ $c->libelle }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Valider</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="5">Aucun intrant.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

@can('create', App\Models\Intrant::class)
<section class="panel">
    <div class="panel-head"><h3>Nouvel intrant</h3></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('intrants.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field span-3">
                    <label class="label" for="nom">Nom</label>
                    <input id="nom" name="nom" class="input" placeholder="ex. NPK 15-15-15" value="{{ old('nom') }}" required>
                </div>
                <div class="field span-3">
                    <label class="label" for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="select">
                        @foreach ($categories as $c)
                            <option value="{{ $c }}" @selected(old('categorie') === $c)>{{ ucfirst($c) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field span-2">
                    <label class="label" for="unite">Unité</label>
                    <input id="unite" name="unite" class="input" value="{{ old('unite', 'kg') }}" required>
                </div>
                <div class="field span-2">
                    <label class="label" for="seuil_minimum">Seuil minimum</label>
                    <input id="seuil_minimum" type="number" step="0.01" min="0" name="seuil_minimum" class="input" value="{{ old('seuil_minimum') }}" required>
                </div>
                <div class="field span-2">
                    <label class="label" for="stock_initial">Stock initial</label>
                    <input id="stock_initial" type="number" step="0.01" min="0" name="stock_initial" class="input" value="{{ old('stock_initial') }}">
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><x-icon name="plus" /> Créer l'intrant</button>
            </div>
        </form>
    </div>
</section>
@endcan

<section class="panel">
    <div class="panel-head"><h3>Derniers mouvements</h3></div>
    <div class="table-wrap">
        <table class="table table-compact">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Intrant</th>
                    <th>Type</th>
                    <th class="text-end">Quantité</th>
                    <th class="text-end">Stock après</th>
                    <th>Campagne</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($mouvements as $m)
                <tr>
                    <td class="nowrap">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->intrant?->nom }}</td>
                    <td><span class="badge">{{ str_replace('_', ' ', $m->type) }}</span></td>
                    <td class="text-end">{{ $m->quantite }}</td>
                    <td class="text-end">{{ $m->stock_apres }}</td>
                    <td>{{ $m->campagne?->libelle }}</td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="6">Aucun mouvement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
