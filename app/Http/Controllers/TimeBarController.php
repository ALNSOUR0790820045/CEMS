<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Project;
use App\Models\TimeBarAlert;
use App\Models\TimeBarContractualClause;
use App\Models\TimeBarEvent;
use App\Models\TimeBarSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeBarController extends Controller
{
    public function index()
    {
        $events = TimeBarEvent::with(['project', 'contract', 'identifiedBy', 'assignedTo'])
            ->latest()
            ->paginate(20);

        return view('time-bar.events.index', compact('events'));
    }

    public function create()
    {
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $contracts = Contract::where('status', '!=', 'terminated')->get();

        return view('time-bar.events.create', compact('projects', 'contracts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'discovery_date' => 'required|date',
            'event_type' => 'required|in:delay,disruption,variation_instruction,differing_conditions,force_majeure,suspension,client_default,design_error,late_information,access_delay,payment_delay,other',
            'notice_period_days' => 'nullable|integer|min:1|max:90',
            'estimated_delay_days' => 'nullable|integer|min:0',
            'estimated_cost_impact' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'priority' => 'nullable|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // Get notice period from contract or use default
        if (! isset($validated['notice_period_days']) && $request->contract_id) {
            $contract = Contract::find($request->contract_id);
            $validated['notice_period_days'] = $contract->default_notice_period ?? 28;
        } else {
            $validated['notice_period_days'] = $validated['notice_period_days'] ?? 28;
        }

        $validated['identified_by'] = Auth::id();
        $validated['status'] = 'identified';
        $validated['priority'] = $validated['priority'] ?? 'high';
        $validated['currency'] = $validated['currency'] ?? 'JOD';

        $event = TimeBarEvent::create($validated);

        return redirect()->route('time-bar.events.show', $event->id)
            ->with('success', 'تم تسجيل الحدث بنجاح');
    }

    public function show(TimeBarEvent $event)
    {
        $event->load(['project', 'contract', 'identifiedBy', 'assignedTo', 'alerts', 'noticeCorrespondence']);

        return view('time-bar.events.show', compact('event'));
    }

    public function edit(TimeBarEvent $event)
    {
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $contracts = Contract::where('status', '!=', 'terminated')->get();

        return view('time-bar.events.edit', compact('event', 'projects', 'contracts'));
    }

    public function update(Request $request, TimeBarEvent $event)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'discovery_date' => 'required|date',
            'event_type' => 'required|in:delay,disruption,variation_instruction,differing_conditions,force_majeure,suspension,client_default,design_error,late_information,access_delay,payment_delay,other',
            'notice_period_days' => 'required|integer|min:1|max:90',
            'estimated_delay_days' => 'nullable|integer|min:0',
            'estimated_cost_impact' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'priority' => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:identified,notice_pending,notice_sent,claim_submitted,resolved,time_barred,cancelled',
            'notes' => 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()->route('time-bar.events.show', $event->id)
            ->with('success', 'تم تحديث الحدث بنجاح');
    }

    public function sendNotice(Request $request, TimeBarEvent $event)
    {
        $validated = $request->validate([
            'notice_reference' => 'required|string|max:255',
            'correspondence_id' => 'nullable|exists:correspondence,id',
            'notice_date' => 'required|date',
        ]);

        $event->update([
            'notice_sent' => true,
            'notice_sent_date' => $validated['notice_date'],
            'notice_reference' => $validated['notice_reference'],
            'notice_correspondence_id' => $validated['correspondence_id'] ?? null,
            'status' => 'notice_sent',
        ]);

        return redirect()->route('time-bar.events.show', $event->id)
            ->with('success', 'تم تسجيل إرسال الإشعار بنجاح');
    }

    public function dashboard()
    {
        $statistics = [
            'total_events' => TimeBarEvent::count(),
            'active_events' => TimeBarEvent::active()->count(),
            'expiring_soon' => TimeBarEvent::expiring(7)->count(),
            'expired_events' => TimeBarEvent::expired()->count(),
            'notice_sent' => TimeBarEvent::where('notice_sent', true)->count(),
        ];

        $expiringEvents = TimeBarEvent::with(['project', 'contract'])
            ->expiring(7)
            ->orderBy('days_remaining', 'asc')
            ->limit(10)
            ->get();

        $recentAlerts = TimeBarAlert::with(['event.project'])
            ->latest('sent_at')
            ->limit(10)
            ->get();

        $eventsByType = TimeBarEvent::select('event_type', DB::raw('count(*) as count'))
            ->groupBy('event_type')
            ->get();

        $eventsByStatus = TimeBarEvent::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('time-bar.dashboard', compact(
            'statistics',
            'expiringEvents',
            'recentAlerts',
            'eventsByType',
            'eventsByStatus'
        ));
    }

    public function alerts()
    {
        $alerts = TimeBarAlert::with(['event.project', 'acknowledgedBy'])
            ->latest('sent_at')
            ->paginate(20);

        return view('time-bar.alerts', compact('alerts'));
    }

    public function expiring()
    {
        $days = request('days', 7);
        $events = TimeBarEvent::with(['project', 'contract', 'identifiedBy', 'assignedTo'])
            ->expiring($days)
            ->orderBy('days_remaining', 'asc')
            ->paginate(20);

        return view('time-bar.expiring', compact('events', 'days'));
    }

    public function expired()
    {
        $events = TimeBarEvent::with(['project', 'contract', 'identifiedBy', 'assignedTo'])
            ->expired()
            ->latest()
            ->paginate(20);

        return view('time-bar.expired', compact('events'));
    }

    public function statistics()
    {
        $data = [
            'total_events' => TimeBarEvent::count(),
            'active_events' => TimeBarEvent::active()->count(),
            'expiring_soon' => TimeBarEvent::expiring(7)->count(),
            'expired_events' => TimeBarEvent::expired()->count(),
            'notice_sent' => TimeBarEvent::where('notice_sent', true)->count(),
            'events_by_type' => TimeBarEvent::select('event_type', DB::raw('count(*) as count'))
                ->groupBy('event_type')
                ->get(),
            'events_by_status' => TimeBarEvent::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'events_by_priority' => TimeBarEvent::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get(),
            'monthly_events' => TimeBarEvent::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->get(),
        ];

        return response()->json($data);
    }

    public function settings()
    {
        $projectId = request('project_id');
        $contractId = request('contract_id');

        $settings = TimeBarSetting::getForProjectOrContract($projectId, $contractId);

        return view('time-bar.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'default_notice_period' => 'required|integer|min:1|max:90',
            'first_warning_days' => 'required|integer|min:1',
            'second_warning_days' => 'required|integer|min:1',
            'urgent_warning_days' => 'required|integer|min:1',
            'critical_warning_days' => 'required|integer|min:1',
            'final_warning_days' => 'required|integer|min:1',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'escalation_enabled' => 'boolean',
            'notification_recipients' => 'nullable|array',
            'escalation_recipients' => 'nullable|array',
        ]);

        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['sms_notifications'] = $request->has('sms_notifications');
        $validated['escalation_enabled'] = $request->has('escalation_enabled');

        TimeBarSetting::updateOrCreate(
            [
                'project_id' => $validated['project_id'] ?? null,
                'contract_id' => $validated['contract_id'] ?? null,
            ],
            $validated
        );

        return redirect()->route('time-bar.settings')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    public function clauses(Request $request)
    {
        $contractId = $request->get('contract_id');

        $query = TimeBarContractualClause::with('contract');

        if ($contractId) {
            $query->where('contract_id', $contractId);
        }

        $clauses = $query->active()->paginate(20);

        return view('time-bar.clauses', compact('clauses'));
    }

    public function calendar()
    {
        $events = TimeBarEvent::with(['project', 'contract'])
            ->active()
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->notice_deadline->format('Y-m-d'),
                    'url' => route('time-bar.events.show', $event->id),
                    'className' => $this->getEventClass($event),
                    'extendedProps' => [
                        'days_remaining' => $event->days_remaining,
                        'priority' => $event->priority,
                        'status' => $event->status,
                    ],
                ];
            });

        return view('time-bar.calendar', compact('events'));
    }

    public function reports()
    {
        return view('time-bar.reports');
    }

    private function getEventClass(TimeBarEvent $event): string
    {
        if ($event->days_remaining <= 0) {
            return 'event-expired';
        } elseif ($event->days_remaining <= 3) {
            return 'event-critical';
        } elseif ($event->days_remaining <= 7) {
            return 'event-urgent';
        } elseif ($event->days_remaining <= 14) {
            return 'event-warning';
        }

        return 'event-normal';
    }
}
