<?php

declare(strict_types=1);

namespace App\Util\RawLogBuffer;

final class BufferKeyHelper
{
    private const UID_LENGTH = 16; // Must be even

    public const REQUEST_PREFIX = 'raw_log_request';
    public const REFERENCE_PREFIX = 'raw_log_reference';

    public static function makeUid(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return substr(bin2hex(random_bytes(self::UID_LENGTH / 2)), 0, self::UID_LENGTH);
    }

    public static function getUidFromKey(string $key): string
    {
        return self::trimPrefix($key, self::REQUEST_PREFIX)
            ?? self::trimPrefix($key, self::REFERENCE_PREFIX)
            ?? $key;
    }

    public static function makeRequestKey(string $uid): string
    {
        return self::REQUEST_PREFIX . ':' . $uid;
    }

    public static function makeReferenceKey(string $uid): string
    {
        return self::REFERENCE_PREFIX . ':' . $uid;
    }

    private static function trimPrefix(string $key, string $prefix): ?string
    {
        if (str_starts_with($key, $prefix)) {
            return substr($key, strlen($prefix) + 1);
        }

        return null;
    }
}
