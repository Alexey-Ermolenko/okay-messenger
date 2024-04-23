<?php

declare(strict_types=1);

namespace App\Enum;

enum RequestStatus: string
{
    case Success = 'Success';
    case Error = 'Error';
}
