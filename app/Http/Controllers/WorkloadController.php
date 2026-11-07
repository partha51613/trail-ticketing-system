<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class WorkloadController extends Controller
{
    /**
     * Display the workload & statistics page.
     */
    public function index(Request $request): View
    {
        $today = Carbon::today();

        // User Workload: All users sorted by active assigned tickets (heaviest to lightest)
        $allUsers = User::query()
            ->withCount([
                'ticketsAssigned as active_tickets_count' => function ($q) {
                    $q->where('status', '!=', 'closed')
                        ->whereHas('project', function ($p) {
                            $p->withoutTrashed()->where('status', '!=', 'closed');
                        });
                },
                'ticketsAssigned as urgent_tickets_count' => function ($q) {
                    $q->where('status', '!=', 'closed')
                        ->where('priority', 'urgent')
                        ->whereHas('project', function ($p) {
                            $p->withoutTrashed()->where('status', '!=', 'closed');
                        });
                },
                'ticketsAssigned as high_tickets_count' => function ($q) {
                    $q->where('status', '!=', 'closed')
                        ->where('priority', 'high')
                        ->whereHas('project', function ($p) {
                            $p->withoutTrashed()->where('status', '!=', 'closed');
                        });
                },
                'ticketsAssigned as overdue_tickets_count' => function ($q) use ($today) {
                    $q->where('status', '!=', 'closed')
                        ->whereNotNull('due_date')
                        ->where('due_date', '<', $today)
                        ->whereHas('project', function ($p) {
                            $p->withoutTrashed()->where('status', '!=', 'closed');
                        });
                },
            ])
            ->get()
            ->sortByDesc('active_tickets_count')
            ->values();

        // Assign Rank before pagination so rank remains global (#1, #2, etc.)
        $allUsers->each(function ($user, $idx) {
            $user->workload_rank = $idx + 1;
        });

        // Paginate users (10 per page)
        $perPage = 10;
        $currentPage = (int) $request->input('page', 1);
        $currentItems = $allUsers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $users = new LengthAwarePaginator(
            $currentItems,
            $allUsers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Workspace Summary Metrics
        $totalActiveTickets = Ticket::query()
            ->where('status', '!=', 'closed')
            ->whereHas('project', function ($p) {
                $p->withoutTrashed()->where('status', '!=', 'closed');
            })
            ->count();

        $totalUnassignedTickets = Ticket::query()
            ->where('status', '!=', 'closed')
            ->whereNull('assigned_to_id')
            ->whereHas('project', function ($p) {
                $p->withoutTrashed()->where('status', '!=', 'closed');
            })
            ->count();

        $totalAssignedTickets = $totalActiveTickets - $totalUnassignedTickets;
        $heaviestUser = $allUsers->first();

        return view('workload.index', [
            'users' => $users,
            'allUsersCount' => $allUsers->count(),
            'totalActiveTickets' => $totalActiveTickets,
            'totalUnassignedTickets' => $totalUnassignedTickets,
            'totalAssignedTickets' => $totalAssignedTickets,
            'heaviestUser' => $heaviestUser,
        ]);
    }
}
