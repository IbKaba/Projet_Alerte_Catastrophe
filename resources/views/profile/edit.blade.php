@extends(auth()->user()->canModerate() ? 'layouts.app' : 'layouts.citizen')
@section('title','Mon profil')
@section('content')
<div class="page-heading">
    <div><span class="eyebrow">Compte</span><h1>Mon profil</h1><p>Mettez à jour vos informations personnelles et votre mot de passe.</p></div>
</div>

<div class="profile-grid">
    <form method="POST" action="{{ route('profile.update') }}" class="panel form-card">
        @csrf @method('PATCH')
        <div class="panel-heading"><div><h2>Informations personnelles</h2><p>Ces informations sont associées à vos signalements.</p></div></div>
        <div class="form-stack">
            <label>Nom complet<input name="name" value="{{ old('name',$user->name) }}" required maxlength="120" autocomplete="name"></label>
            <label>Email<input type="email" name="email" value="{{ old('email',$user->email) }}" required maxlength="180" autocomplete="email"></label>
            <label>Téléphone <span class="field-optional">optionnel</span><input type="tel" name="phone" value="{{ old('phone',$user->phone) }}" maxlength="30" autocomplete="tel"></label>
            <label>Rôle<input value="{{ $user->role->label() }}" disabled></label>
            <button class="button button-primary" type="submit">Enregistrer les informations</button>
        </div>
    </form>

    <form method="POST" action="{{ route('profile.password') }}" class="panel form-card">
        @csrf @method('PUT')
        <div class="panel-heading"><div><h2>Changer le mot de passe</h2><p>Le mot de passe actuel est demandé avant toute modification.</p></div></div>
        <div class="form-stack">
            <label>Mot de passe actuel<input type="password" name="current_password" required autocomplete="current-password"></label>
            <label>Nouveau mot de passe<input type="password" name="password" required minlength="6" maxlength="128" autocomplete="new-password"></label>
            <label>Confirmation<input type="password" name="password_confirmation" required minlength="6" maxlength="128" autocomplete="new-password"></label>
            <div class="form-hint"><strong>Minimum : 6 caractères.</strong> Pour un compte sensible, utilisez de préférence une phrase de passe plus longue et unique.</div>
            <button class="button button-dark" type="submit">Modifier le mot de passe</button>
        </div>
    </form>
</div>
@endsection
