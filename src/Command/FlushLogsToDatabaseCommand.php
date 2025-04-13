<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Redis;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'logs:flush-bulk-to-db',
    description: 'Flush HTTP request logs from Redis to PostgreSQL in bulk using SCAN'
)]
class FlushLogsToDatabaseCommand extends Command
{
    public function __construct(
        private readonly Redis $redis,
        private readonly Connection $connection
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = 100;
        $sleepSeconds = 5;

        $output->writeln(
            '<info>Long-runner is running. Every '
            . $sleepSeconds
            . ' seconds it will try to flush logs from Redis (via SCAN).</info>'
        );

        while (true) {
            try {
                $iterator = null;
                $count = 100;
                $logs = [];
                $processedKeys = [];

                do {
                    $this->redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);

                    $keys = $this->redis->scan($iterator, 'raw_log_request:*', $count);

                    foreach ($keys as $key) {
                        $log = $this->redis->hGetAll($key);

                        if (!empty($log)) {
                            $logs[] = $log;
                            $processedKeys[] = $key;
                        }

                        if (count($logs) >= $batchSize) {
                            break 2;
                        }
                    }
                } while ($iterator !== 0);

                if (count($logs) > 0) {
                    $sql = "INSERT INTO raw_logs (
                        requested_at,
                        responded_at,
                        status,
                        request_headers,
                        request_body,
                        response_headers,
                        response_body
                    ) VALUES ";

                    $values = [];
                    $params = [];
                    foreach ($logs as $log) {
                        $values[] = "(?, ?, ?, ?, ?, ?, ?)";
                        $params[] = $log['requested_at'] ?? null;
                        $params[] = $log['responded_at'] ?? null;
                        $params[] = $log['status'] ?? null;
                        $params[] = $log['request_headers'] ?? null;
                        $params[] = $log['request_body'] ?? null;
                        $params[] = $log['response_headers'] ?? null;
                        $params[] = $log['response_body'] ?? null;
                    }

                    $sql .= implode(', ', $values);
                    $this->connection->executeStatement($sql, $params);

                    // Удаляем обработанные ключи
                    foreach ($processedKeys as $key) {
                        $this->redis->del($key);
                    }

                    $msg = '<info>Recorded ' . count($logs) . ' raw logs into DB and deleted from Redis.</info>';

                    $output->writeln($msg);
                } else {
                    $output->writeln('<comment>No logs for recording.</comment>');
                }

                sleep($sleepSeconds);
            } catch (\Throwable $e) {
                $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
                sleep($sleepSeconds);
            }
        }
    }
}
