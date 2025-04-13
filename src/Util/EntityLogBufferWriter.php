<?php

declare(strict_types=1);

namespace App\Util;

use App\DTO\LogDTO;
use App\Util\RawLogBuffer\BufferKeyHelper;
use JsonException;
use Psr\Log\LoggerInterface;
use Redis;
use RedisException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class EntityLogBufferWriter
{
    public function __construct(
        #[Autowire(service: 'snc_redis.entity_log')]
        private Redis $redis,
        private LoggerInterface $logger,
    ) {
    }

    private function prepareHeaders(iterable $headers): string
    {
        try {
            return json_encode($headers, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return $e->getMessage();
        }
    }

    private function writeLog(string $requestId, array $data): bool
    {
        $requestKey = BufferKeyHelper::makeEntityRequestKey($requestId);

        try {
            /** @noinspection PhpRedundantOptionalArgumentInspection */
            $this->redis->multi(Redis::MULTI);
            foreach ($data as $key => $value) {
                $this->redis->hSet($requestKey, $key, $value);
            }

            $this->redis->exec();
        } catch (RedisException $e) {
            $this->logger->error('Failed to write entity log to Redis: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function writeEntityLog(LogDTO $log): bool
    {
        $data = [
            'entity_type' => $log->entityType,
            'entity_id' => $log->entityId,
            'action' => $log->action,
            'data' => $log->data,
            'created_at' => $log->createdAt,
            'user_id' => $log->user_id,
            'ip_address' => $log->ipAddress,
            'route' => $log->requestRoute,
        ];

        return $this->writeLog(BufferKeyHelper::makeUid(), $data);
    }

    /** @throws RedisException */
    public function deleteEntityLogBuffer(string $requestId): void
    {
        $this->redis->del(
            BufferKeyHelper::makeEntityRequestKey($requestId),
            BufferKeyHelper::makeEntityReferenceKey($requestId),
        );
    }
}
