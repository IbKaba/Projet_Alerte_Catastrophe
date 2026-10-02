<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Utiliser directement storage_path évite que realpath() retourne false
    | lorsque le répertoire de vues compilées vient d'être recréé après une
    | extraction d'archive. Blade créera au besoin les sous-répertoires.
    |
    */
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
];
