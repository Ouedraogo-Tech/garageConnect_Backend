<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">{{ $vehicule->marque }} {{ $vehicule->modele }}</h5>
        <h6 class="card-subtitle mb-2 text-muted">{{ $vehicule->immatriculation }}</h6>
        <p class="card-text">
            {{ $vehicule->couleur }} — {{ $vehicule->annee }} — {{ $vehicule->kilometrage }} km<br>
            {{ $vehicule->carrosserie }} · {{ $vehicule->energie }} · {{ $vehicule->boite }}
        </p>
        <a href="{{ route('vehicules.showView', $vehicule) }}" class="btn btn-sm btn-primary">Voir détail</a>
    </div>
</div>