<?php

declare(strict_types=1);

namespace App\Message;

final readonly class EmailMessage
{
    public function __construct(
        private string $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
