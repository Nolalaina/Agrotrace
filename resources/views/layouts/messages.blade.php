@if (session('succes'))
    <div class="alert alert-success" role="status">
        <x-icon name="check" />
        <div class="alert-body">{{ session('succes') }}</div>
        <button type="button" class="alert-close" data-dismiss aria-label="Fermer"><x-icon name="x" /></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <x-icon name="alert" />
        <div class="alert-body">
            @if ($errors->count() === 1)
                {{ $errors->first() }}
            @else
                <ul>@foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach</ul>
            @endif
        </div>
        <button type="button" class="alert-close" data-dismiss aria-label="Fermer"><x-icon name="x" /></button>
    </div>
@endif
