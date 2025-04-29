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
    public function run(): void
    {

        $this->call([
            AdminUserSeeder::class,
        ]);

    }
}
