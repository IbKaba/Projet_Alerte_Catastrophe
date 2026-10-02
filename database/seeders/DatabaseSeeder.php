<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Disaster;
use App\Models\DisasterCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Risques naturels', 'description' => 'Phénomènes naturels pouvant menacer les personnes et les infrastructures.'],
            ['name' => 'Incendies', 'description' => 'Feux domestiques, industriels, de végétation ou d’infrastructure.'],
            ['name' => 'Risques technologiques', 'description' => 'Accidents industriels, électriques, chimiques ou liés aux infrastructures.'],
            ['name' => 'Urgences sanitaires', 'description' => 'Événements sanitaires collectifs nécessitant une vigilance particulière.'],
        ];

        foreach ($categories as $data) {
            DisasterCategory::firstOrCreate(['name' => $data['name']], $data + ['is_active' => true]);
        }

        $natural = DisasterCategory::where('name', 'Risques naturels')->first();
        $fire = DisasterCategory::where('name', 'Incendies')->first();
        $tech = DisasterCategory::where('name', 'Risques technologiques')->first();

        $disasters = [
            [$natural?->id, 'Inondation', 'Évitez les zones submergées et les cours d’eau en crue.'],
            [$natural?->id, 'Glissement de terrain', 'Éloignez-vous des pentes instables et signalez toute fissure ou mouvement de terrain.'],
            [$natural?->id, 'Tempête / vents violents', 'Mettez-vous à l’abri et éloignez-vous des structures fragiles.'],
            [$fire?->id, 'Incendie', 'Évacuez la zone, ne revenez pas sur vos pas et contactez les secours.'],
            [$tech?->id, 'Accident industriel', 'Éloignez-vous du site et respectez les consignes des autorités.'],
            [$tech?->id, 'Effondrement de bâtiment', 'N’entrez pas dans la structure et gardez une distance de sécurité.'],
        ];

        foreach ($disasters as [$categoryId, $name, $instructions]) {
            if ($categoryId) {
                Disaster::firstOrCreate(
                    ['category_id' => $categoryId, 'name' => $name],
                    ['instructions' => $instructions, 'is_active' => true]
                );
            }
        }

        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminEmail && $adminPassword) {
            Validator::make(
                ['email' => $adminEmail, 'password' => $adminPassword],
                ['email' => ['required', 'email:rfc'], 'password' => ['required', Password::defaults()]],
            )->validate();

            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => env('ADMIN_NAME', 'Administrateur principal'),
                    'phone' => env('ADMIN_PHONE'),
                    'password' => Hash::make($adminPassword),
                    'role' => Role::ADMIN,
                    'is_active' => true,
                ]
            );
        }
    }
}
