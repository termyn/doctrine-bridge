<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Types\UuidsType;

use Termyn\Bridge\Doctrine\Types\UuidsType;
use Termyn\Uuid;
use Termyn\Uuid\Symfony\SymfonyUuid;

final class SymfonyUuidsType extends UuidsType
{
    public const NAME = 'termyn.uuids_symfony';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function covertToUuid(string $uuid): Uuid
    {
        return SymfonyUuid::fromString($uuid);
    }
}
