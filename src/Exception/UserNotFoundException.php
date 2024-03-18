<?php

declare(strict_types=1);

namespace App\Exception;

class UserNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('user not found');
    }
}
