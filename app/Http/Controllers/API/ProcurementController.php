<?php

namespace App\Http\Controllers\API;

use App\Exceptions\BussinessException;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Services\ProcureService;

class ProcurementController extends Controller
{
    public function __construct(private ProcureService $procureservice) {}

    public function index(Request $request)
    {
        try {
            $data = $this->procureservice->getAll($request->user(), $request->all());

            return ResponseFormatter::success($data, 'Data po retrieved.');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function deliver(Request $request, string $id)
    {
        try {
            $data = $this->procureservice->deliver($id, $request->user(), $request);
            return ResponseFormatter::success($data, 'Purchase Order delivered', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
