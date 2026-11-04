<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();

        if ($projects->isEmpty()) {
            $this->command->warn('No projects found. Please run SampleDataSeeder first.');

            return;
        }

        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please create users first.');

            return;
        }

        $assignableUsers = $users; // Any user can be assigned

        $ticketsData = [
            // Project Alpha
            [
                'title' => 'Fix SSO Authentication redirect loop',
                'description' => 'Users report infinite redirect loop when accessing the portal through corporate Azure AD SSO.',
                'status' => 'in_progress',
                'priority' => 'urgent',
                'due_offset' => 2,
                'assignee' => true,
            ],
            [
                'title' => 'Optimize slow SQL queries on dashboard overview',
                'description' => 'Metrics calculation takes over 4.2 seconds during peak hours. Needs index audit and caching.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_offset' => 5,
                'assignee' => true,
            ],
            [
                'title' => 'Update Tailwind color tokens to warm brand palette',
                'description' => 'Migrate hardcoded hex values across views to use the new brand palette definition.',
                'status' => 'closed',
                'priority' => 'medium',
                'due_offset' => -3,
                'assignee' => true,
            ],
            [
                'title' => 'Triage customer webhook timeout exceptions',
                'description' => 'Webhooks sent to Stripe endpoint are occasionally dropping due to strict 3s timeout threshold.',
                'status' => 'new',
                'priority' => 'high',
                'due_offset' => 1,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Implement multi-tenant project isolation checks',
                'description' => 'Ensure user cannot access tickets in unassigned projects via direct URL tampering.',
                'status' => 'new',
                'priority' => 'medium',
                'due_offset' => 7,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Investigate Redis cache eviction anomalies',
                'description' => 'Eviction policy is dropping session tokens instead of query results when memory limit is reached.',
                'status' => 'blocked',
                'priority' => 'high',
                'due_offset' => 4,
                'assignee' => true,
            ],
            [
                'title' => 'Prepare release notes for v1.4 deployment',
                'description' => 'Compile changelog and notify stakeholders prior to staging migration.',
                'status' => 'waiting',
                'priority' => 'low',
                'due_offset' => 3,
                'assignee' => true,
            ],

            // Project Beta
            [
                'title' => 'Design new onboarding checklist component',
                'description' => 'Modular card component showing initial setup steps for newly onboarded team members.',
                'status' => 'new',
                'priority' => 'medium',
                'due_offset' => 6,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Add automated end-to-end browser tests',
                'description' => 'Setup Playwright or Dusk to test critical ticket creation and status update paths.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_offset' => 8,
                'assignee' => true,
            ],
            [
                'title' => 'Fix mobile viewport horizontal scroll in project view',
                'description' => 'Table element overflows container width on viewports narrower than 640px.',
                'status' => 'waiting',
                'priority' => 'high',
                'due_offset' => -1,
                'assignee' => true,
            ],
            [
                'title' => 'Implement CSV export for weekly ticket reports',
                'description' => 'Provide downloadable CSV with timestamps formatted in Asia/Kolkata timezone.',
                'status' => 'new',
                'priority' => 'low',
                'due_offset' => 12,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Review and update third-party npm dependencies',
                'description' => 'Run security audit and bump minor packages with zero breaking changes.',
                'status' => 'in_progress',
                'priority' => 'low',
                'due_offset' => 10,
                'assignee' => true,
            ],
            [
                'title' => 'Database connection pool exhaustion during backup',
                'description' => 'Nightly backup script holds open transactions, causing incoming HTTP requests to stall.',
                'status' => 'blocked',
                'priority' => 'urgent',
                'due_offset' => 1,
                'assignee' => true,
            ],

            // Project C / Across projects
            [
                'title' => 'Configure transactional email templates',
                'description' => 'Design clean, branded Blade email templates for ticket assignment and status notifications.',
                'status' => 'new',
                'priority' => 'medium',
                'due_offset' => 4,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Fix avatar letter fallback for single character usernames',
                'description' => 'Ensure proper uppercase conversion and contrasting background colors.',
                'status' => 'closed',
                'priority' => 'low',
                'due_offset' => -5,
                'assignee' => true,
            ],
            [
                'title' => 'Audit user permissions for admin-only project deletion',
                'description' => 'Verify policy checks reject non-admin users attempting DELETE requests.',
                'status' => 'waiting',
                'priority' => 'high',
                'due_offset' => -2,
                'assignee' => true,
            ],
            [
                'title' => 'Integrate Sentry error tracking with Slack notifications',
                'description' => 'Route 500 server errors directly to internal incident channel.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_offset' => 5,
                'assignee' => true,
            ],
            [
                'title' => 'Refactor CommentController 5-minute edit window logic',
                'description' => 'Ensure boundary condition tests accurately verify 300-second cutoff threshold.',
                'status' => 'closed',
                'priority' => 'medium',
                'due_offset' => -4,
                'assignee' => true,
            ],
            [
                'title' => 'Review database indexes for activity log lookups',
                'description' => 'Add compound index on ticket_id and created_at to speed up ticket history queries.',
                'status' => 'new',
                'priority' => 'high',
                'due_offset' => 3,
                'assignee' => false, // Unassigned
            ],
            [
                'title' => 'Implement dark mode toggle prototype',
                'description' => 'Investigate Tailwind CSS variables for seamless dark palette transitions.',
                'status' => 'new',
                'priority' => 'low',
                'due_offset' => 14,
                'assignee' => false, // Unassigned
            ],
        ];

        $projectCount = $projects->count();

        foreach ($ticketsData as $index => $data) {
            $project = $projects[$index % $projectCount];
            $creator = $users->random();
            $assignee = $data['assignee'] ? $assignableUsers->random() : null;

            Ticket::updateOrCreate(
                [
                    'title' => $data['title'],
                    'project_id' => $project->id,
                ],
                [
                    'description' => $data['description'],
                    'created_by_id' => $creator->id,
                    'assigned_to_id' => $assignee?->id,
                    'status' => $data['status'],
                    'priority' => $data['priority'],
                    'due_date' => now()->addDays($data['due_offset'])->toDateString(),
                ]
            );
        }

        $this->command->info(count($ticketsData).' tickets successfully seeded across '.$projectCount.' projects!');
    }
}
