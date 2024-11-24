<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\RawLogDTO;
use App\Util\RawLogsWriter;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequestLoggerService
{
    private const DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private readonly Security $security,
        private readonly RawLogsWriter $logsWriter,
    ) {
    }

    private function jsonEncode(mixed $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\JsonException $e) {
            return json_encode(['err' => $e->getMessage()], JSON_INVALID_UTF8_SUBSTITUTE) ?: '{}';
        }
    }

    /**
     * @throws Exception
     */
    public function logRequest(
        Request $request,
        Response $response,
        \DateTimeImmutable $requestedAt,
        \DateTimeImmutable $respondedAt
    ): void {
        $record = new RawLogDTO(
            id: null,
            requestedAt: ($requestedAt)->format(self::DATETIME_FORMAT),
            respondedAt: ($respondedAt)->format(self::DATETIME_FORMAT),
            status: (string) $response->getStatusCode(),
            requestHeaders: $this->jsonEncode($request->headers->all()),
            requestBody: $request->getContent(),
            responseHeaders: $this->jsonEncode($response->headers->all()),
            responseBody: $response->getContent(),
        );

        $this->logsWriter->write($record);
    }
}
