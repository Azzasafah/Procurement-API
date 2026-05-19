<?php

namespace App\Http\Controllers\API;

use App\Exceptions\BussinessException;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProcureRequest;
use App\Http\Requests\CreateRequestRequest;
use App\Http\Requests\UpdateRequestRequest;
use App\Models\ProcurementRequest;
use App\Services\RequestService;
use Exception;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct(private RequestService $requestservice) {}

    public function index(Request $request)
    {
        try {
            $data = $this->requestservice->getAll($request->user(), $request->all());

            return ResponseFormatter::success($data, 'Data request retrieved.');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function store(CreateRequestRequest $request)
    {
        try {
            $data = $this->requestservice->create($request->user(), $request->validated(), $request);

            return ResponseFormatter::success($data, 'Request created', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(UpdateRequestRequest $request, string $id)
    {
        try {
            $procurementRequest = ProcurementRequest::findOrFail($id);
            $data = $this->requestservice->update($procurementRequest, $request->user(), $request->validated());

            return ResponseFormatter::success($data, 'Request updated', 200);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        try {
            $procurementRequest  = ProcurementRequest::findOrFail($id);
            $this->requestservice->delete($procurementRequest, $request->user());

            return ResponseFormatter::success('Request deleted', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function submit(Request $request, string $id)
    {
        try {
            $data = $this->requestservice->submit($id, $request->user(), $request);
            return ResponseFormatter::success($data, 'Request submitted', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function approve(Request $request, string $id)
    {
        try {
            $data = $this->requestservice->approve($id, $request->user(), $request->input('notes'), $request);
            return ResponseFormatter::success($data, 'Request submitted', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }


    public function reject(Request $request, string $id)
    {
        try {
            $data = $this->requestservice->reject($id, $request->user(), $request->input('reason'), $request);
            return ResponseFormatter::success($data, 'Request rejected', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function procure(CreateProcureRequest $request, string $id)
    {
        try {
            $data = $this->requestservice->procure($id, $request->user(), $request->validated(), $request);
            return ResponseFormatter::success($data, 'Purchase Order request created', 201);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function complete(Request $request, string $id)
    {
        try {
            $data = $this->requestservice->complete($id, $request->user(), $request);
            return ResponseFormatter::success($data, 'Purchase Order completed', 200);
        } catch (BussinessException $e) {
            return ResponseFormatter::error($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
