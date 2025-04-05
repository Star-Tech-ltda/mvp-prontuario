<?php

namespace Database\Seeders;

use App\Models\AssessmentGroup;
use App\Models\Patient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Patient::factory()->count(5)->create();

        $groups = [
            'ESTADO NUTRICIONAL', 'NÍVEL DE CONSCIÊNCIA', 'MOVIMENTAÇÃO', 'PELE/TECIDOS',
            'CRÂNIO', 'ABDÔMEN', 'OLHOS', 'OUVIDOS', 'NARIZ', 'BOCA', 'PESCOÇO', 'TÓRAX',
            'MAMAS', 'OXIGENAÇÃO', 'DIETA HOSPITALAR', 'VIAS DE ALIMENTAÇÃO', 'TIPOS DE CURATIVO',
            'CONTEÚDO DE CURATIVOS', 'CONTEÚDO MICROBIANO', 'SONDAGEM', 'GENITURINÁRIO',
            'MEMBROS SUPERIORES', 'MEMBROS INFERIORES', 'ELIMINAÇÃO INTESTINAL'
        ];

        foreach ($groups as $group) {
            AssessmentGroup::create(['name' => $group]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
    }
}
