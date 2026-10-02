@extends(auth()->user()->canModerate() ? 'layouts.app' : 'layouts.citizen')
@section('title','Nouveau signalement')
@section('content')
<div class="page-heading"><div><span class="eyebrow">Nouveau signalement</span><h1>Signaler une catastrophe</h1><p>Décrivez simplement la situation, indiquez sa localisation et ajoutez des photos si vous pouvez le faire sans danger.</p></div></div>
<div class="form-layout">
    <form method="POST" action="{{ route('alerts.store') }}" enctype="multipart/form-data" class="panel form-card">
        @csrf
        <div class="panel-heading"><div><h2>Informations du signalement</h2><p>Les champs obligatoires permettent d’identifier et de localiser l’événement.</p></div></div>
        @include('alerts._form')
        <div class="form-actions"><a class="button button-ghost" href="{{ auth()->user()->canModerate() ? route('alerts.index') : route('citizen.home') }}">Annuler</a><button class="button button-primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Envoyer le signalement</button></div>
    </form>
    <aside class="panel side-panel sticky-panel">
        <div class="tip-icon"><i class="fa-solid fa-shield-heart"></i></div>
        <h2>Avant d’envoyer</h2>
        <ul class="checklist"><li>Ne vous mettez jamais en danger pour prendre une photo.</li><li>Indiquez la localisation la plus précise possible.</li><li>Décrivez seulement les faits réellement observés.</li><li>En urgence vitale, contactez d’abord les services de secours.</li></ul>
        <div class="callout info"><strong>Après l’envoi</strong><p>Votre alerte commence au statut « En attente ». Un modérateur pourra ensuite la valider ou vous demander une correction.</p></div>
    </aside>
</div>
@endsection
