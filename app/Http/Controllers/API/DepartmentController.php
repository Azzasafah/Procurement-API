<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDepartmentRequest;
use App\Models\Department;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with('users');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        return ResponseFormatter::success($query->paginate($request->input('limit', 10)), 'Get data department successfully');
    }

    public function show(string $id)
    {
        $department = Department::find($id);

        if (!$department) {
            return ResponseFormatter::error('Department not found', 404);
        }

        return ResponseFormatter::success($department, 'Department found');
    }

    public function store(CreateDepartmentRequest $request)
    {
        try {
            $department = Department::create($request->validated());

            return ResponseFormatter::success($department->load('users'), 'Department created', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function update(CreateDepartmentRequest $request, string $id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return ResponseFormatter::error('Department not found', 404);
            }

            $department->update($request->validated());
            return ResponseFormatter::success($department->fresh(), 'Department updated');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function delete(string $id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return ResponseFormatter::error('Department not found', 404);
            }

            $department->delete();

            return ResponseFormatter::success(null, 'Department deleted');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }
}
