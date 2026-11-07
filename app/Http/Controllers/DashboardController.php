<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Metric Stats Cards (for active projects)
        $assignedCount = Ticket::whereHas('project', fn ($p) => $p->where('status', '!=', 'closed'))
            ->where('assigned_to_id', $user->id)
            ->where('status', '!=', 'closed')
            ->count();

        $createdCount = Ticket::whereHas('project', fn ($p) => $p->where('status', '!=', 'closed'))
            ->where('created_by_id', $user->id)
            ->where('status', '!=', 'closed')
            ->count();

        $watchedCount = Ticket::whereHas('project', fn ($p) => $p->where('status', '!=', 'closed'))
            ->whereHas('watchers', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', '!=', 'closed')->count();

        // Urgent / High priority requiring attention
        $urgentQuery = Ticket::whereHas('project', fn ($p) => $p->where('status', '!=', 'closed'))
            ->where('status', '!=', 'closed')
            ->whereIn('priority', ['urgent', 'high']);

        if ($user->role !== 'admin') {
            $urgentQuery->where(function ($query) use ($user) {
                $query->where('assigned_to_id', $user->id)
                    ->orWhere('created_by_id', $user->id)
                    ->orWhereHas('project.managers', function ($managerQuery) use ($user) {
                        $managerQuery->where('users.id', $user->id);
                    });
            });
        }
        $urgentCount = $urgentQuery->count();

        // 2. High Priority / Needs Attention Tickets (Assigned or Managed)
        $attentionTicketsQuery = Ticket::whereHas('project', fn ($p) => $p->where('status', '!=', 'closed'))
            ->with(['project', 'assignedTo', 'creator'])
            ->where('status', '!=', 'closed');

        if ($user->role === 'admin') {
            // Admins see high priority tickets across all projects
            $attentionTicketsQuery->whereIn('priority', ['urgent', 'high', 'medium']);
        } else {
            $attentionTicketsQuery->where(function ($query) use ($user) {
                $query->where('assigned_to_id', $user->id)
                    ->orWhere(function ($pmQuery) use ($user) {
                        $pmQuery->whereHas('project.managers', function ($m) use ($user) {
                            $m->where('users.id', $user->id);
                        })->whereIn('priority', ['urgent', 'high']);
                    });
            });
        }

        $attentionTickets = $attentionTicketsQuery
            ->orderByRaw("CASE priority
                WHEN 'urgent' THEN 1
                WHEN 'high' THEN 2
                WHEN 'medium' THEN 3
                WHEN 'low' THEN 4
                ELSE 5
            END")
            ->orderBy('due_date', 'asc')
            ->limit(8)
            ->get();

        // 3. Recent Activity Logs (Tickets user is involved with or all if admin)
        $activityLogsQuery = TicketActivityLog::with(['ticket.project', 'user'])
            ->whereHas('ticket')
            ->latest();

        if ($user->role !== 'admin') {
            $activityLogsQuery->whereHas('ticket', function ($query) use ($user) {
                $query->where('assigned_to_id', $user->id)
                    ->orWhere('created_by_id', $user->id)
                    ->orWhereHas('watchers', function ($w) use ($user) {
                        $w->where('user_id', $user->id);
                    })
                    ->orWhereHas('project.managers', function ($m) use ($user) {
                        $m->where('users.id', $user->id);
                    });
            });
        }

        $recentActivities = $activityLogsQuery->limit(8)->get();

        return view('dashboard', [
            'assignedCount' => $assignedCount,
            'createdCount' => $createdCount,
            'watchedCount' => $watchedCount,
            'urgentCount' => $urgentCount,
            'attentionTickets' => $attentionTickets,
            'recentActivities' => $recentActivities,
        ]);
    }
}
