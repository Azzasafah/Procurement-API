<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ProcurementOrder;
use App\Models\ProcurementRequest;
use App\Models\Stock;
use App\Models\Vendor;
use App\Services\ReportService;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected RequestService $requestService,
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $actor = Auth::user();
        $role = $actor->role;

        $stats = [
            'total_requests' => ProcurementRequest::count(),
            'pending_review' => ProcurementRequest::where('status', ProcurementRequest::STATUS_SUBMITTED)->count(),
            'approved'       => ProcurementRequest::where('status', ProcurementRequest::STATUS_APPROVED)->count(),
            'in_procurement' => ProcurementRequest::where('status', ProcurementRequest::STATUS_IN_PROCUREMENT)->count(),
            'completed'      => ProcurementRequest::where('status', ProcurementRequest::STATUS_COMPLETED)->count(),
            'rejected'       => ProcurementRequest::where('status', ProcurementRequest::STATUS_REJECTED)->count(),
            'my_drafts'      => ProcurementRequest::where('requester_id', $actor->id)->where('status', ProcurementRequest::STATUS_DRAFT)->count(),
            'low_stocks'     => Stock::whereColumn('quantity', '<=', 'minimum_stock')->count(),
            'pending_orders' => ProcurementOrder::where('status', 'ordered')->count(),
            'total_stocks'   => Stock::count(),
            'vendors'        => Vendor::where('is_active', true)->count(),
        ];

        // Mapping for compatibility with view variables
        $counts = [
            'draft'           => $stats['my_drafts'],
            'submitted'       => $stats['pending_review'],
            'approved'        => $stats['approved'],
            'in_procurement'  => $stats['in_procurement'],
            'completed'       => $stats['completed'],
            'rejected'        => $stats['rejected'],
            'total_requests'  => $stats['total_requests'],
            'critical_stocks' => $stats['low_stocks'],
            'total_stocks'    => $stats['total_stocks'],
            'vendors'         => $stats['vendors'],
        ];

        $leadTime = $this->reportService->averageLeadTime();

        // Recent requests scoped to actor
        $recentRequestsQuery = ProcurementRequest::with(['requester', 'department'])
            ->latest();

        if ($role === 'employee') {
            $recentRequestsQuery->where('requester_id', $actor->id);
        }

        $recentRequests = $recentRequestsQuery->limit(8)->get();

        return view('dashboard.index', compact('stats', 'counts', 'leadTime', 'recentRequests', 'actor'));
    }
}
