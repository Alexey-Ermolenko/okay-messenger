<?php

declare(strict_types=1);

namespace App\Handler;

use App\DTO\LogDTO;
use App\DTO\RawLogDTO;
use App\Util\RawLogsWriter;
use Doctrine\DBAL\Exception;
use Monolog\Handler\AbstractHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Processor\PsrLogMessageProcessor;

class MonologDBHandler extends AbstractHandler
{
    private const MESSAGE_MAX_LENGTH = 512;
    private const LONG_MESSAGE_SUFFIX = '[..truncated]';
    private const DATETIME_FORMAT = 'Y-m-d H:i:s.u';
    private const MAX_ROWS = 100;

    private static bool $enabled = true;

    public function __construct(
        private readonly RawLogsWriter $logsWriter,
        private readonly PsrLogMessageProcessor $logMessageProcessor,
        Level $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    private function prepareMessage(LogRecord $record): string
    {
        $message = $record->message;

        if (strlen($message) > self::MESSAGE_MAX_LENGTH) {
            $message = substr($message, 0, self::MESSAGE_MAX_LENGTH - strlen(self::LONG_MESSAGE_SUFFIX));
            $message .= self::LONG_MESSAGE_SUFFIX;
        }

        return $message;
    }

    private function jsonEncode(mixed $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\JsonException $e) {
            return json_encode(['err' => $e->getMessage()], JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}';
        }
    }

    public function handle(LogRecord $record): bool
    {
        if (!self::$enabled || !$this->isHandling($record)) {
            return false;
        }

        $written = $this->write($record);

        return $written && (false === $this->bubble);
    }

    /**
     * @param array<LogRecord> $records
     */
    public function handleBatch(array $records): void
    {
        if (!self::$enabled) {
            return;
        }

        $records = array_filter($records, fn (LogRecord $record) => $this->isHandling($record));

        foreach (array_chunk($records, self::MAX_ROWS) as $chunk) {
            $this->writeBatch($chunk);
        }
    }

    protected function write(LogRecord $record): bool
    {
        try {
            $this->logsWriter->write($this->prepareRecord($record));

            return true;
        } catch (Exception) {
            // Disable logger on any DB error
            self::$enabled = false;

            return false;
        }
    }

    /** @param LogRecord[] $records */
    private function writeBatch(array $records): void
    {
        if (empty($records)) {
            return;
        }

        $logs = array_map(
            fn (LogRecord $record): RawLogDTO => $this->prepareRecord($record),
            $records
        );

        try {
            $this->logsWriter->writeBatch($logs);
        } catch (Exception) {
            // Disable logger on any DB error
            self::$enabled = false;
        }
    }

    private function prepareContext(LogRecord $record): string
    {
        return $this->jsonEncode($record->context);
    }

    private function prepareRecord(LogRecord $record): ?RawLogDTO
    {
        $record = ($this->logMessageProcessor)($record);

        //        return new RawLogDTO(
        //            requestedAt: '',
        //            respondedAt: '',
        //            status: '',
        //            requestHeaders: '',
        //            requestBody: '',
        //            responseHeaders: '',
        //            responseBody: ''
        //        );

        //        /** @var string $level */
        //        $level = $record->level->getName();
        //        return new LogDTO(
        //            $level,
        //            $record->channel,
        //            $record->datetime->format(self::DATETIME_FORMAT),
        //            $this->prepareMessage($record),
        //            $this->prepareContext($record),
        //        );
    }
}
