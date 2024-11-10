<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\SampleMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

#[AsCommand(
    name: "app:send"
)]
class SendMessageCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        #$msg = "Message sent: " . time() . "\n";
        $msg = (string)json_encode([
            'fromEmail' => 'test@test.com',
            'toEmail' => 'test@test2.com',
        ]);

        $this->messageBus->dispatch(new SampleMessage($msg), [
            new DelayStamp(1000),
        ]);

        return Command::SUCCESS;
    }
}
