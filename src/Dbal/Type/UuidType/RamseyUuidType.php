<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type\UuidType;

use Termyn\Bridge\Doctrine\Dbal\Type\UuidType;
use Termyn\Uuid\Ramsey\RamseyUuid;

final class RamseyUuidType extends UuidType
{
    public const NAME = 'termyn.uuid_ramsey';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function covertToUuid(string $uuid): RamseyUuid
    {
        return RamseyUuid::fromString($uuid);
    }
}
