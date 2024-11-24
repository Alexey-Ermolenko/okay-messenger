<?php

declare(strict_types=1);

namespace App\Handler;

use App\Message\SampleMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SampleMessageHandler
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public function __invoke(SampleMessage $sampleMessage): void
    {
        print_r('Handler handled the message!');
        $this->logger->warning('Handler handled the message!');
    }
}
