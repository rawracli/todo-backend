<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Free',
            'description' => 'Free plan with basic features',
            'price' => 0,
            'task_limit' => 1,
        ]);

        Plan::create([
            'name' => 'Pro',
            'description' => 'Pro plan with advanced features',
            'price' => 9.99,
            'task_limit' => 10,
        ]);

        Plan::create([
            'name' => 'Company',
            'description' => 'Premium plan with all features',
            'price' => 19.99,
            'task_limit' => 100,
        ]);
    }
}
