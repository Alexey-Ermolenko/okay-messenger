<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer;

use App\DTO\RawLogDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class LogBufferWriter
{
    public function __construct(
        #[Autowire(service: 'snc_redis.raw_log')]
        private \Redis $redis,
        private LoggerInterface $logger,
    ) {
    }

    private function prepareHeaders(iterable $headers): string
    {
        try {
            return json_encode($headers, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return $e->getMessage();
        }
    }

    private function writeLog(string $requestId, array $data): bool
    {
        $requestKey = BufferKeyHelper::makeRequestKey($requestId);

        try {
            /* @noinspection PhpRedundantOptionalArgumentInspection */
            $this->redis->multi(\Redis::MULTI);
            foreach ($data as $key => $value) {
                $this->redis->hSet($requestKey, $key, $value);
            }

            $this->redis->exec();
        } catch (\RedisException $e) {
            $this->logger->error('Failed to write raw log to Redis: '.$e->getMessage());

            return false;
        }

        return true;
    }

    public function writeRequest(RawLogDTO $rawLog): bool
    {
        $data = [
            'requested_at' => $rawLog->requestedAt,
            'responded_at' => $rawLog->respondedAt,
            'status' => $rawLog->status,
            'request_headers' => $rawLog->requestHeaders,
            'request_body' => $rawLog->requestBody,
            'response_headers' => $rawLog->responseHeaders,
            'response_body' => $rawLog->responseBody,
        ];

        return $this->writeLog(BufferKeyHelper::makeUid(), $data);
    }

    /** @throws \RedisException */
    public function deleteLogBuffer(string $requestId): void
    {
        $this->redis->del(
            BufferKeyHelper::makeRequestKey($requestId),
            BufferKeyHelper::makeReferenceKey($requestId),
        );
    }
}
