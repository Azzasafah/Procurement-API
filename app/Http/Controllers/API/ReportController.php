<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function summary()
    {
        try {
            return ResponseFormatter::success($this->reportService->summary(), 'Dashboard Summary');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function topDepartments()
    {
        try {
            return ResponseFormatter::success(
                $this->reportService->topDepartments(),
                'Top 5 departments with many requests for the last 3 months.'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function categoryPerMonth()
    {
        try {
            return ResponseFormatter::success(
                $this->reportService->categoryPerMonth(),
                'Most requested item categories per month.'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function averageLeadTime()
    {
        try {
            return ResponseFormatter::success(
                $this->reportService->averageLeadTime(),
                'Average processing time from SUBMITTED to COMPLETED.'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
