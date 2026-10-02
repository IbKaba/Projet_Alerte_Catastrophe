<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--email=} {--name=}';

    protected $description = 'Créer ou promouvoir un compte administrateur de façon interactive';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) ($this->option('email') ?: $this->ask('Adresse email de l’administrateur'))));
        $name = trim((string) ($this->option('name') ?: $this->ask('Nom complet', 'Administrateur principal')));
        $password = (string) $this->secret('Mot de passe (6 caractères minimum)');
        $confirmation = (string) $this->secret('Confirmez le mot de passe');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'email' => ['required', 'email:rfc'],
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill([
            'name' => $name,
            'password' => Hash::make($password),
            'role' => Role::ADMIN,
            'is_active' => true,
        ]);
        $user->save();

        $this->info("Administrateur prêt : {$user->email}");

        return self::SUCCESS;
    }
}
