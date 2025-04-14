<?php

namespace Database\Seeders;

use App\Enums\Severity;
use App\Models\AssessmentGroup;
use App\Models\AssessmentOption;
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
    public function run()
    {
        $options = [
            'ABDOME' => [
                Severity::NONE->value => 'Abdômen sem anormalidades palpáveis ou sinais visíveis.',
                Severity::LOW->value => 'Dor leve ou desconforto localizado sem sinais inflamatórios.',
                Severity::MEDIUM->value => 'Sensibilidade ou dor moderada, presença de distensão abdominal leve.',
                Severity::HIGH->value => 'Dor intensa com sinais de peritonite ou outros achados significativos.',
                Severity::CRITICAL->value => 'Abdômen agudo, com necessidade de intervenção imediata.',
            ],
            'BOCA' => [
                Severity::NONE->value => 'Mucosas saudáveis e sem lesões visíveis.',
                Severity::LOW->value => 'Pequena ulceração ou irritação leve.',
                Severity::MEDIUM->value => 'Inflamação moderada, presença de aftas ou sangramento gengival.',
                Severity::HIGH->value => 'Lesões extensas, infecção visível ou dor persistente.',
                Severity::CRITICAL->value => 'Abscesso com risco sistêmico ou comprometimento das vias aéreas.',
            ],
            'CRÂNIO' => [
                Severity::NONE->value => 'Sem alterações visíveis ou dor à palpação.',
                Severity::LOW->value => 'Pequena área sensível ou hematoma superficial.',
                Severity::MEDIUM->value => 'Trauma leve a moderado com desconforto persistente.',
                Severity::HIGH->value => 'Fratura evidente ou sinais neurológicos associados.',
                Severity::CRITICAL->value => 'Lesão grave com comprometimento das funções vitais.',
            ],
            'OLHOS' => [
                Severity::NONE->value => 'Visão normal, sem sinais de irritação.',
                Severity::LOW->value => 'Olhos ligeiramente vermelhos ou coceira leve.',
                Severity::MEDIUM->value => 'Conjuntivite ou dificuldade moderada na visão.',
                Severity::HIGH->value => 'Lesões na córnea ou perda parcial da visão.',
                Severity::CRITICAL->value => 'Perda completa da visão ou lesões graves nos olhos.',
            ],
            'OUVIDOS' => [
                Severity::NONE->value => 'Ouvidos saudáveis, sem queixas.',
                Severity::LOW->value => 'Leve desconforto ou sensação de pressão.',
                Severity::MEDIUM->value => 'Infecção moderada ou redução parcial da audição.',
                Severity::HIGH->value => 'Dor intensa com secreção ou febre associada.',
                Severity::CRITICAL->value => 'Surdez súbita, trauma grave ou infecção que ameaça tecidos vizinhos.',
            ],
        ];

        foreach ($options as $groupName => $severities) {
            // Busca o grupo existente pelo nome
            $group = AssessmentGroup::where('name', $groupName)->first();

            if ($group) {
                foreach ($severities as $severity => $description) {
                    // Cria as opções relacionadas ao grupo
                    AssessmentOption::create([
                        'assessment_group_id' => $group->id,
                        'description' => $description,
                        'custom_phrase' => $description, // Mesmo texto usado aqui
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
