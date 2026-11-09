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

        $statuses = ['new', 'in_progress', 'blocked', 'waiting', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];

        $sampleTopics = [
            'Fix SSO authentication redirect loop',
            'Optimize slow SQL queries on dashboard overview',
            'Update Tailwind color tokens to warm brand palette',
            'Triage customer webhook timeout exceptions',
            'Implement multi-tenant project isolation checks',
            'Investigate Redis cache eviction anomalies',
            'Prepare release notes for staging deployment',
            'Design new onboarding checklist component',
            'Add automated end-to-end browser tests',
            'Fix mobile viewport horizontal scroll in project view',
            'Implement CSV export for weekly ticket reports',
            'Review and update third-party npm dependencies',
            'Database connection pool exhaustion during backup',
            'Configure transactional email templates',
            'Fix avatar letter fallback for single character usernames',
            'Audit user permissions for admin-only project deletion',
            'Integrate Sentry error tracking with Slack notifications',
            'Refactor CommentController 5-minute edit window logic',
            'Review database indexes for activity log lookups',
            'Implement dark mode toggle prototype',
            'Sanitize rich text content in comment inputs',
            'Add rate limiting to public invitation endpoints',
            'Setup automated weekly database snapshot backups',
            'Memory leak in real-time notification socket listener',
            'Improve accessibility contrast on muted label text',
            'Fix timezone offset rendering for Asia/Kolkata users',
            'Support bulk ticket reassignment from project overview',
            'Optimize avatar image thumbnail caching on CDN',
            'Refactor ticket policy to allow watcher self-unsubscribing',
            'Audit soft deletes cascade on ticket deletion',
        ];

        $currentTicketCount = Ticket::count();
        $targetCount = 5;
        $needed = max(0, $targetCount - $currentTicketCount);

        if ($needed === 0) {
            $this->command->info("Already have {$currentTicketCount} tickets in the database.");

            return;
        }

        for ($i = 1; $i <= $needed; $i++) {
            $project = $projects->random();
            $creator = $users->random();
            // 70% assigned, 30% unassigned
            $isAssigned = (mt_rand(1, 100) <= 70);
            $assignee = $isAssigned ? $users->random() : null;

            $topic = $sampleTopics[array_rand($sampleTopics)].' #'.($currentTicketCount + $i);
            $status = $statuses[array_rand($statuses)];
            $priority = $priorities[array_rand($priorities)];
            $dueDaysOffset = mt_rand(-10, 20);

            Ticket::create([
                'title' => $topic,
                'description' => "Detailed context and acceptance criteria for: {$topic}. Please review the attached notes, verify requirements, and update status accordingly.",
                'project_id' => $project->id,
                'created_by_id' => $creator->id,
                'assigned_to_id' => $assignee?->id,
                'status' => $status,
                'priority' => $priority,
                'due_date' => now()->addDays($dueDaysOffset)->toDateString(),
            ]);
        }

        $totalNow = Ticket::count();
        $this->command->info("Successfully seeded {$needed} new tickets. Total tickets in database: {$totalNow}");
    }
}
