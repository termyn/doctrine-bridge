<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type\UuidsType;

use Termyn\Bridge\Doctrine\Dbal\Type\UuidsType;
use Termyn\Uuid;
use Termyn\Uuid\Ramsey\RamseyUuid;

final class RamseyUuidsType extends UuidsType
{
    public const NAME = 'termyn.uuids_ramsey';

    public function getName(): string
    {
        return self::NAME;
    }

    protected function covertToUuid(string $uuid): Uuid
    {
        return RamseyUuid::fromString($uuid);
    }
}
