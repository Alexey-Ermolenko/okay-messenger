<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Table(name: 'logs')]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'level', type: Types::STRING, length: 255)]
    private string $level;

    #[ORM\Column(name: 'channel', type: Types::STRING, length: 255)]
    private string $channel;

    #[ORM\Column(name: 'datetime', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $datetime;

    #[ORM\Column(name: 'message', type: Types::STRING, length: 512)]
    private string $message;

    #[ORM\Column(name: 'context', type: 'json')]
    private array $context;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getDatetime(): \DateTimeImmutable
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeImmutable $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }
}
