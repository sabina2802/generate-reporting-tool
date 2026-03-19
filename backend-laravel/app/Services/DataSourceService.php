<?php

namespace App\Services;

use App\Models\DataSource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Exception;

class DataSourceService
{
    /**
     * Get all data sources with optional filters
     *
     * @param array $filters
     * @return Collection|LengthAwarePaginator
     */
    public function getAll(array $filters = []): Collection|LengthAwarePaginator
    {
        $query = DataSource::query();

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply type filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Apply status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        // Apply date range filter
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Return paginated or all results
        if (!empty($filters['per_page'])) {
            return $query->paginate($filters['per_page']);
        }

        return $query->get();
    }

    /**
     * Get data source by ID
     *
     * @param int $id
     * @return DataSource
     * @throws Exception
     */
    public function getById(int $id): DataSource
    {
        $dataSource = DataSource::find($id);

        if (!$dataSource) {
            throw new Exception("Data source with ID {$id} not found.");
        }

        return $dataSource;
    }

    /**
     * Create a new data source
     *
     * @param array $data
     * @return DataSource
     * @throws Exception
     */
    public function create(array $data): DataSource
    {
        try {
            DB::beginTransaction();

            $dataSource = DataSource::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'connection_config' => $data['connection_config'] ?? [],
                'query_config' => $data['query_config'] ?? [],
                'is_active' => $data['is_active'] ?? true,
                'refresh_interval' => $data['refresh_interval'] ?? null,
                'last_synced_at' => null,
                'created_by' => auth()->id(),
            ]);

            // Test connection if configuration is provided
            if (!empty($data['connection_config'])) {
                $this->testConnection($dataSource);
            }

            DB::commit();

            return $dataSource->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to create data source: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing data source
     *
     * @param DataSource $dataSource
     * @param array $data
     * @return DataSource
     * @throws Exception
     */
    public function update(DataSource $dataSource, array $data): DataSource
    {
        try {
            DB::beginTransaction();

            $dataSource->update([
                'name' => $data['name'] ?? $dataSource->name,
                'description' => $data['description'] ?? $dataSource->description,
                'type' => $data['type'] ?? $dataSource->type,
                'connection_config' => $data['connection_config'] ?? $dataSource->connection_config,
                'query_config' => $data['query_config'] ?? $dataSource->query_config,
                'is_active' => $data['is_active'] ?? $dataSource->is_active,
                'refresh_interval' => $data['refresh_interval'] ?? $dataSource->refresh_interval,
                'updated_by' => auth()->id(),
            ]);

            // Test connection if configuration was updated
            if (isset($data['connection_config']) && !empty($data['connection_config'])) {
                $this->testConnection($dataSource);
            }

            DB::commit();

            return $dataSource->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to update data source: ' . $e->getMessage());
        }
    }

    /**
     * Delete a data source
     *
     * @param DataSource $dataSource
     * @return bool
     * @throws Exception
     */
    public function delete(DataSource $dataSource): bool
    {
        try {
            DB::beginTransaction();

            // Check if data source is used in any reports
            if ($dataSource->reports()->exists()) {
                throw new Exception('Cannot delete data source that is being used in reports.');
            }

            $deleted = $dataSource->delete();

            DB::commit();

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Failed to delete data source: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to data source
     *
     * @param DataSource $dataSource
     * @return bool
     * @throws Exception
     */
    public function testConnection(DataSource $dataSource): bool
    {
        try {
            switch ($dataSource->type) {
                case 'mysql':
                    return $this->testMysqlConnection($dataSource->connection_config);
                case 'postgresql':
                    return $this->testPostgresqlConnection($dataSource->connection_config);
                case 'api':
                    return $this->testApiConnection($dataSource->connection_config);
                case 'csv':
                    return $this->testCsvConnection($dataSource->connection_config);
                default:
                    throw new Exception('Unsupported data source type: ' . $dataSource->type);
            }
        } catch (Exception $e) {
            throw new Exception('Connection test failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync data from data source
     *
     * @param DataSource $dataSource
     * @return array
     * @throws Exception
     */
    public function syncData(DataSource $dataSource): array
    {
        try {
            if (!$dataSource->is_active) {
                throw new Exception('Data source is not active.');
            }

            $data = $this->fetchDataFromSource($dataSource);
            
            $dataSource->update([
                'last_synced_at' => now(),
                'sync_status' => 'success',
                'sync_message' => 'Data synced successfully.',
            ]);

            return $data;
        } catch (Exception $e) {
            $dataSource->update([
                'sync_status' => 'failed',
                'sync_message' => $e->getMessage(),
            ]);
            throw new Exception('Failed to sync data: ' . $e->getMessage());
        }
    }

    /**
     * Test MySQL connection
     *
     * @param array $config
     * @return bool
     */
    private function testMysqlConnection(array $config): bool
    {
        // Implementation for MySQL connection test
        return true;
    }

    /**
     * Test PostgreSQL connection
     *
     * @param array $config
     * @return bool
     */
    private function testPostgresqlConnection(array $config): bool
    {
        // Implementation for PostgreSQL connection test
        return true;
    }

    /**
     * Test API connection
     *
     * @param array $config
     * @return bool
     */
    private function testApiConnection(array $config): bool
    {
        // Implementation for API connection test
        return true;
    }

    /**
     * Test CSV connection
     *
     * @param array $config
     * @return bool
     */
    private function testCsvConnection(array $config): bool
    {
        // Implementation for CSV connection test
        return true;
    }

    /**
     * Fetch data from data source
     *
     * @param DataSource $dataSource
     * @return array
     */
    private function fetchDataFromSource(DataSource $dataSource): array
    {
        // Implementation for fetching data from various sources
        return [];
    }
}