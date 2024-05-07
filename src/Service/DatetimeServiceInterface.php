<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Psr\Clock\ClockInterface;

interface DatetimeServiceInterface extends ClockInterface
{
    public function now(): DateTimeImmutable;
}
