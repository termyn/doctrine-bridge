<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Types\UuidType;

use Termyn\Bridge\Doctrine\Types\UuidType;
use Termyn\Uuid\Symfony\SymfonyUuid;

final class SymfonyUuidType extends UuidType
{
    public const NAME = 'termyn.uuid_symfony';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function covertToUuid(string $uuid): SymfonyUuid
    {
        return SymfonyUuid::fromString($uuid);
    }
}
