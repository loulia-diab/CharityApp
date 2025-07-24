<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
    /*
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        $this->call(AdminSeeder::class);
        $this->call([DaySeeder::class,]);
        $this->call(VolunteeringTypeSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(CampaignSeeder::class);
        $this->call([
            BeneficiaryRequestSeeder::class,
            BeneficiarySeeder::class, // بعده لربط المستفيدين
        ]);
        $this->call(HumanCaseSeeder::class);
        $this->call(SponsorshipSeeder::class);
        $this->call([BoxSeeder::class,]);
    }
}
