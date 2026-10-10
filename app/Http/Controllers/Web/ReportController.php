<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index()
    {
        $leadTime = $this->reportService->averageLeadTime();
        $topDepartments = $this->reportService->topDepartments();
        $categoryPerMonth = $this->reportService->categoryPerMonth();
        $summary = $this->reportService->summary();

        return view('reports.index', compact(
            'leadTime',
            'topDepartments',
            'categoryPerMonth',
            'summary'
        ));
    }
}
