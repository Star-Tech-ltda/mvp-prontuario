<?php

namespace Database\Seeders;

use App\Enums\Severity;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('assessment_groups')->delete();


        $options = [
            'ESTADO GERAL' => [
                Severity::NONE->value => 'Estado geral bom - BEG',
                Severity::LOW->value => 'Estado geral regular - REG',
                Severity::MEDIUM->value => 'Estado geral ruim - REG',
            ],
            'CONSCIÊNCIA' => [
                Severity::NONE->value => 'Consciente',
                Severity::LOW->value => 'Obnubilado',
                Severity::MEDIUM->value => 'Letárgico',
                Severity::HIGH->value => 'Confuso',
                Severity::CRITICAL->value => 'Sonolento',
            ],
            'ORIENTAÇÃO' => [
                Severity::NONE->value => 'Orientado no tempo e no espaço',
                Severity::LOW->value => 'Desorientado no tempo e no espaço',
            ],
            'COMUNICAÇÃO' => [
                Severity::NONE->value => 'Comunica-se verbalmente',
                Severity::LOW->value => 'Dificuldades de comunicação',
            ],
            'HUMOR/ESTADO EMOCIONAL' => [
                Severity::NONE->value => 'Alegre',
                Severity::LOW->value => 'Triste depressivo',
                Severity::MEDIUM->value => 'Agressivo',
                Severity::HIGH->value => 'Melancólico',
                Severity::CRITICAL->value => 'Choroso',
            ],
            'HIDRATAÇÃO' => [
                Severity::NONE->value => 'Hidratado',
                Severity::LOW->value => 'Desidratado',
            ],
            'ESTADO NUTRICIONAL' => [
                Severity::NONE->value => 'Caquético',
                Severity::LOW->value => 'Desnutrido',
                Severity::MEDIUM->value => 'Nutrido',
                Severity::HIGH->value => 'Obeso',
            ],
            'PELE' => [
                Severity::NONE->value => 'Íntegra',
                Severity::LOW->value => 'Presença de Lesões',
                Severity::MEDIUM->value => 'Normocorado',
                Severity::HIGH->value => 'Pálido',
                Severity::CRITICAL->value => 'Cianótico',
            ],
            'HIGIENE CORPORAL' => [
                Severity::NONE->value => 'Satisfatória',
                Severity::LOW->value => 'Insatisfatória',
            ],
        ];



        foreach ($options as $groupName => $severities) {
            // Busca o grupo existente pelo nome
            $group = AssessmentGroup::updateOrCreate(
                ['name' => $groupName],
                ['slug' => \Str::slug($groupName)]
            );


            if ($group) {
                foreach ($severities as $severity => $description) {
                    // Cria as opções relacionadas ao grupo
                    AssessmentOption::create([
                        'assessment_group_id' => $group->id,
                        'description' => $description,
                        'severity' => $severity,
                    ]);
                }
            } else {
                // Log ou exceção caso o grupo não seja encontrado
                echo "Grupo '$groupName' não encontrado no banco de dados.\n";
            }
        }
    }
}
