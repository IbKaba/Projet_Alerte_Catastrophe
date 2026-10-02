@extends('layouts.guest')
@section('title', 'Réinitialiser le mot de passe')
@section('content')
<section class="auth-single-page">
    <div class="auth-card">
        <div class="auth-heading left">
            <span class="auth-step">Sécurité</span>
            <h2>Choisir un nouveau mot de passe</h2>
            <p>Le mot de passe doit contenir au moins 6 caractères. Une longueur supérieure est recommandée pour mieux protéger le compte.</p>
        </div>
        @if($errors->any())<div class="form-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('password.store') }}" class="form-stack">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <label>Email
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email">
            </label>
            <label>Nouveau mot de passe
                <input type="password" name="password" required minlength="6" maxlength="128" autocomplete="new-password">
            </label>
            <label>Confirmation
                <input type="password" name="password_confirmation" required minlength="6" maxlength="128" autocomplete="new-password">
            </label>
            <button class="button button-primary button-full" type="submit">Réinitialiser le mot de passe</button>
        </form>
    </div>
</section>
@endsection
