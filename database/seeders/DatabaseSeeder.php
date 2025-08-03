<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            //MppSeeder::class,
            //InstitutionGroupSeeder::class,
            //InstitutionSeeder::class,
            //OccupationSeeder::class,
            //EducationSeeder::class,
            //QuestionSeeder::class,
            //ChoiceSeeder::class,
            //UnsurSeeder::class,
            //AnswerSeeder::class,
            //ServiceSeeder::class,
            //SuperAdminSeeder::class,
            RoleSeeder::class,
        ]);
        //User::factory()->create([
            //'name' => 'Test User',
            //'email' => 'test@example.com',
        //]);
    }
}
