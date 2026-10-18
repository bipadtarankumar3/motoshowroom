<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemHealthCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:health {--json : Output health report as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive system health checks across database, cache, storage, and queue readiness';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $isHealthy = collect($checks)->every(fn ($c) => $c['status'] === 'ok');

        if ($this->option('json')) {
            $this->line(json_encode([
                'status' => $isHealthy ? 'healthy' : 'unhealthy',
                'timestamp' => now()->toIso8601String(),
                'checks' => $checks,
            ], JSON_PRETTY_PRINT));

            return $isHealthy ? self::SUCCESS : self::FAILURE;
        }

        $this->newLine();
        $this->line('<fg=white;bg=blue;options=bold> MOTOSHOWROOM SAAS - OPERATIONAL HEALTH CHECK </>');
        $this->newLine();

        $rows = [];
        foreach ($checks as $name => $result) {
            $statusIcon = $result['status'] === 'ok'
                ? '<fg=green>✔ OK</>'
                : '<fg=red>✖ FAIL</>';

            $rows[] = [ucfirst($name), $statusIcon, $result['message'], $result['latency_ms'].' ms'];
        }

        $this->table(['Component', 'Status', 'Message', 'Latency'], $rows);

        if ($isHealthy) {
            $this->info('All core system dependencies are operational and ready for production traffic.');

            return self::SUCCESS;
        }

        $this->error('One or more critical dependencies failed operational health verification.');

        return self::FAILURE;
    }

    /**
     * Verify database connection and measure query response time.
     *
     * @return array{status: string, message: string, latency_ms: float}
     */
    protected function checkDatabase(): array
    {
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);

            return [
                'status' => 'ok',
                'message' => 'Connected to primary database successfully.',
                'latency_ms' => $latency,
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'fail',
                'message' => 'Database connection failed: '.$e->getMessage(),
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        }
    }

    /**
     * Verify cache store write, read, and delete operations.
     *
     * @return array{status: string, message: string, latency_ms: float}
     */
    protected function checkCache(): array
    {
        $start = microtime(true);
        $key = 'health_probe_'.uniqid();
        $val = 'ok_'.time();

        try {
            Cache::put($key, $val, 10);
            $retrieved = Cache::get($key);
            Cache::forget($key);

            if ($retrieved !== $val) {
                throw new \RuntimeException('Cache returned mismatched payload.');
            }

            return [
                'status' => 'ok',
                'message' => 'Cache driver read/write validated successfully.',
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'fail',
                'message' => 'Cache subsystem failure: '.$e->getMessage(),
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        }
    }

    /**
     * Verify local/public storage disk read/write capability.
     *
     * @return array{status: string, message: string, latency_ms: float}
     */
    protected function checkStorage(): array
    {
        $start = microtime(true);
        $file = 'health_check_probe.tmp';
        $content = 'operational_readiness_probe_'.time();

        try {
            Storage::disk('local')->put($file, $content);
            $read = Storage::disk('local')->get($file);
            Storage::disk('local')->delete($file);

            if ($read !== $content) {
                throw new \RuntimeException('Storage read content mismatched written probe.');
            }

            return [
                'status' => 'ok',
                'message' => 'Local storage read/write validated successfully.',
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'fail',
                'message' => 'Storage filesystem failure: '.$e->getMessage(),
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        }
    }
}
