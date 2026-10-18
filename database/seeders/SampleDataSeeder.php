<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Managers
        $manager1 = User::updateOrCreate(
            ['email' => 'manager1@example.com'],
            ['name' => 'Alice Manager', 'password' => bcrypt('password'), 'role' => 'manager']
        );

        $manager2 = User::updateOrCreate(
            ['email' => 'manager2@example.com'],
            ['name' => 'Bob Manager', 'password' => bcrypt('password'), 'role' => 'manager']
        );

        // 2. Create Regular Users
        $user1 = User::updateOrCreate(
            ['email' => 'user1@example.com'],
            ['name' => 'Charlie User', 'password' => bcrypt('password'), 'role' => 'user']
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@example.com'],
            ['name' => 'Diana User', 'password' => bcrypt('password'), 'role' => 'user']
        );

        // 3. Create Projects
        $projectA = Project::updateOrCreate(
            ['name' => 'Project Alpha'],
            ['description' => 'First core business project.']
        );

        $projectB = Project::updateOrCreate(
            ['name' => 'Project Beta'],
            ['description' => 'Secondary client services project']
        );

        // 4. Assign Managers to Projects (Alice -> Alpha, Bob -> Beta)
        $projectA->managers()->syncWithoutDetaching([$manager1->id]);
        $projectB->managers()->syncWithoutDetaching([$manager2->id]);

        // Create sample tickets
        Ticket::updateOrCreate(
            [
                'title' => 'Initial Server Setup',
                'project_id' => $projectA->id,
            ],
            [
                'description' => 'Create SVG',
                'created_by_id' => $user2->id,
                'assigned_to_id' => $user2->id,
                'status' => 'new',
                'priority' => 'medium',
                'due_date' => now()->addDays(7)->toDateString(),
            ]
        );

        

    }
}
