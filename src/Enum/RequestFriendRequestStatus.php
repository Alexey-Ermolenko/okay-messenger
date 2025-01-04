<?php

declare(strict_types=1);

namespace App\Enum;

enum RequestFriendRequestStatus: string
{
    case pending = 'PENDING';
    case accepted = 'ACCEPTED';
    case deleted = 'DELETED';
}
