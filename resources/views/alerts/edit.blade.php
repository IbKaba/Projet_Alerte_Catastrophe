@extends(auth()->user()->canModerate() ? 'layouts.app' : 'layouts.citizen')
@section('title','Modifier le signalement')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Correction du signalement</span><h1>Modifier l’alerte #{{ $alert->id }}</h1><p>Après l’enregistrement, l’alerte repassera au statut « En attente » afin d’être réexaminée.</p></div></div>
<form method="POST" action="{{ route('alerts.update',$alert) }}" enctype="multipart/form-data" class="panel form-card form-card-wide">
    @csrf @method('PUT')
    @include('alerts._form')
    <div class="form-actions"><a class="button button-ghost" href="{{ route('alerts.show',$alert) }}">Annuler</a><button class="button button-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button></div>
</form>
@endsection
