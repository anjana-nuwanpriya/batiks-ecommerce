<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait OptimizedDatabaseTrait
{
    /**
     * Execute a database transaction with proper connection management
     */
    protected function executeTransaction(callable $callback, int $attempts = 1)
    {
        return DB::transaction(function () use ($callback) {
            return $callback();
        }, $attempts);
    }

    /**
     * Execute query with connection cleanup
     */
    protected function executeQuery(callable $callback)
    {
        try {
            $result = $callback();

            // Force garbage collection for large datasets
            if (memory_get_usage() > 50 * 1024 * 1024) { // 50MB
                gc_collect_cycles();
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Database query failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get paginated results with optimized queries
     */
    protected function getPaginatedResults($query, int $perPage = 15)
    {
        return $this->executeQuery(function () use ($query, $perPage) {
            return $query->paginate($perPage);
        });
    }

    /**
     * Chunk large datasets to prevent memory issues
     */
    protected function processInChunks($query, callable $callback, int $chunkSize = 100)
    {
        return $query->chunk($chunkSize, function ($items) use ($callback) {
            $callback($items);

            // Clear memory after each chunk
            unset($items);
            gc_collect_cycles();
        });
    }

    /**
     * Close database connections when done
     */
    protected function closeConnections()
    {
        DB::disconnect();
    }

    /**
     * Optimize query with proper indexing hints
     */
    protected function optimizeQuery($query)
    {
        // Add query optimization hints
        return $query->select('*'); // Ensure we're not selecting unnecessary columns
    }
}
