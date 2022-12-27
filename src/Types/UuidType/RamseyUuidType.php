<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Types\UuidType;

use Termyn\Bridge\Doctrine\Types\UuidType;
use Termyn\Uuid\Ramsey\RamseyUuid;

final class RamseyUuidType extends UuidType
{
    public const NAME = 'termyn.ramsey_uuid';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function covertToUuid(string $uuid): RamseyUuid
    {
        return RamseyUuid::fromString($uuid);
    }
}
