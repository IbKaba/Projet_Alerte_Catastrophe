<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('components.pagination');

        // Exigence fonctionnelle du projet : 6 caractères minimum.
        // La règle est centralisée pour l'inscription, la réinitialisation,
        // le profil et la création d'un administrateur.
        Password::defaults(fn (): Password => Password::min(6)->max(128));
    }
}
