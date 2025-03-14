<?php

declare(strict_types=1);

namespace App\Serializer;

class CircularReferenceHandler
{
    public function __invoke($object)
    {
        // return sprintf('Circular Reference to %s', get_class($object));
        return str_replace("\u0000", '', json_encode((array) $object));
    }
}
