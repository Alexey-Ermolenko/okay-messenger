<?php

declare(strict_types=1);

namespace App\Handler;

use App\Message\EmailMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class EmailMessageHandler
{
    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    public function __invoke(EmailMessage $sampleMessage): void
    {
        print_r('Handler handled the message!');
        $this->logger->warning('Handler handled the message!');
    }
}
