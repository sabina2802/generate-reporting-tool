<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataSource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class DataSourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a paginated listing of data sources.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $perPage = min(max((int) $perPage, 1), 100);
            
            $query = DataSource::query();
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            }
            
            // Filter by type
            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }
            
            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }
            
            $dataSources = $query->latest()->paginate($perPage);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data sources retrieved successfully',
                'data' => $dataSources
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data sources',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created data source.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:data_sources,name',
                'type' => 'required|string|in:database,api,file,csv,json,xml',
                'description' => 'nullable|string|max:1000',
                'connection_string' => 'nullable|string|max:1000',
                'host' => 'nullable|string|max:255',
                'port' => 'nullable|integer|min:1|max:65535',
                'database_name' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string|max:255',
                'api_url' => 'nullable|url|max:1000',
                'api_key' => 'nullable|string|max:500',
                'file_path' => 'nullable|string|max:500',
                'configuration' => 'nullable|json',
                'status' => 'required|string|in:active,inactive,pending,error',
                'refresh_interval' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dataSource = DataSource::create(array_merge(
                $validator->validated(),
                ['user_id' => auth()->id()]
            ));

            return response()->json([
                'status' => 'success',
                'message' => 'Data source created successfully',
                'data' => $dataSource
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create data source',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified data source.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $dataSource = DataSource::findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data source retrieved successfully',
                'data' => $dataSource
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data source not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data source',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified data source.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $dataSource = DataSource::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:data_sources,name,' . $id,
                'type' => 'sometimes|required|string|in:database,api,file,csv,json,xml',
                'description' => 'nullable|string|max:1000',
                'connection_string' => 'nullable|string|max:1000',
                'host' => 'nullable|string|max:255',
                'port' => 'nullable|integer|min:1|max:65535',
                'database_name' => 'nullable|string|max:255',
                'username' => 'nullable|string|max:255',
                'password' => 'nullable|string|max:255',
                'api_url' => 'nullable|url|max:1000',
                'api_key' => 'nullable|string|max:500',
                'file_path' => 'nullable|string|max:500',
                'configuration' => 'nullable|json',
                'status' => 'sometimes|required|string|in:active,inactive,pending,error',
                'refresh_interval' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $dataSource->update($validator->validated());
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data source updated successfully',
                'data' => $dataSource->fresh()
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data source not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data source',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified data source.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $dataSource = DataSource::findOrFail($id);
            $dataSource->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data source deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data source not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete data source',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test connection to the specified data source.
     */
    public function testConnection(string $id): JsonResponse
    {
        try {
            $dataSource = DataSource::findOrFail($id);
            
            // Here you would implement actual connection testing logic
            // This is a placeholder implementation
            $connectionStatus = $this->performConnectionTest($dataSource);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Connection test completed',
                'data' => [
                    'connected' => $connectionStatus,
                    'tested_at' => now()
                ]
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data source not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Connection test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perform actual connection test (placeholder implementation).
     */
    private function performConnectionTest(DataSource $dataSource): bool
    {
        // Implement actual connection testing logic based on data source type
        // This is a simplified placeholder
        switch ($dataSource->type) {
            case 'database':
                // Test database connection
                return true;
            case 'api':
                // Test API endpoint
                return true;
            case 'file':
            case 'csv':
            case 'json':
            case 'xml':
                // Test file accessibility
                return true;
            default:
                return false;
        }
    }
}