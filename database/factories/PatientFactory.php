<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Patient::class;
    public function definition(): array
    {
        return [
            'name'=>$this->faker->name(),
            'birth_date'=>$this->faker->date(),
            'cpf' => str_pad(rand(10000000000, 99999999999), 11, '0', STR_PAD_LEFT),
            'sex' => $this->faker->randomElement([Sex::MALE->value, Sex::FEMALE->value]),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'internment_reason' => $this->faker->randomElement([
                'Febre Alta',
                'Infecção',
                'Dor de Cabeça',
                'Dor Abdominal',
                'Acidente de Trânsito',
                'Problemas Respiratórios',
                'Insuficiência Cardíaca',
                'Monitoramento Pós-Operatório',
            ]),
            'internment_date' => $this->faker->date(),
            'internment_time' => $this->faker->time(),
            'internment_location' => $this->faker->word(),
            'bed' => 'Leito ' . $this->faker->numberBetween(1, 20),
            'diagnosis' => $this->faker->words(3, true),
        ];
    }
}
