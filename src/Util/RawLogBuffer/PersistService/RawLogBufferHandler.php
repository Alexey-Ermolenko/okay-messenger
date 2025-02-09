<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer\PersistService;

use App\Util\RawLogBuffer\PersistService\DTO\BufferLogMessage;
use Doctrine\DBAL\Exception as ExceptionDBAL;
use Psr\Log\LoggerInterface;
use RedisException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RawLogBufferHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        #private readonly RawLogWriter $persistWriter,
        #private readonly RawLogPgsqlWriter $pgsqlWriter,
        #private readonly RawLogBufferReader $bufferReader,
        #private readonly RawLogBufferWriter $bufferWriter,
    ) {
    }
    //  php ./bin/console messenger:consume redis_transport --time-limit=300 --memory-limit=128M --limit=1000


    public function __invoke(BufferLogMessage $logMessage): void
    {
        $requestId = '123123';
        //$requestId = $logMessage->requestId;

        try {
            $rawLogData = '111';
            #$rawLogData = $this->bufferReader->readRawLog($requestId);
            #$references = $this->bufferReader->readReferences($requestId);
        } catch (RedisException $e) {
            $this->logger->error("Failed to fetch raw log requestId=`$requestId` from Redis: " . $e->getMessage());
            return;
        }

        try {
            #$baseLog = $this->messageFactory->createBaseLog($rawLogData);
            #$contentLog = $this->messageFactory->createContentLog($rawLogData);
            $baseLog = '1';
            $contentLog = '2';
            try {
                $this->logger->info("7777");
                #$this->persistWriter->persistWholeLog($references, $baseLog, $contentLog);
                #$this->pgsqlWriter->persistWholeLog($references, $baseLog, $contentLog);
                #$this->bufferWriter->deleteLogBuffer($requestId);

                #$this->eventDispatcher->dispatch(new BufferLogMessage());
                #$this->messageBus->dispatch(new EmailMessage($msg));
            } catch (ExceptionDBAL $e) {
                $references = [1,1];
                $refs = implode(',', $references);
                $this->logger->error(
                    "Failed to persist log with requestId=$requestId and references=($refs): " . $e->getMessage(),
                    ['requestId' => $requestId, 'references' => $references, 'data' => [$baseLog, $contentLog]],
                );
            }
        } catch (RedisException $e) {
            $this->logger->error("Failed to delete raw log requestId=`$requestId` from Redis: " . $e->getMessage());
        }
    }
}
