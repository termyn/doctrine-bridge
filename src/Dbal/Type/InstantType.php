<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Termyn\DateTime\Instant;

final class InstantType extends Type
{
    public const NAME = 'termyn.instant';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTzTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform,
    ): ?string {
        if (! $value instanceof Instant) {
            return null;
        }

        $dateTime = new DateTimeImmutable(
            datetime: sprintf('@%s', $value->epochSeconds->value),
            timezone: new DateTimeZone('UTC'),
        );

        return $dateTime->format(
            $platform->getDateTimeFormatString()
        );
    }

    public function convertToPHPValue(
        $value,
        AbstractPlatform $platform,
    ): ?Instant {
        if ($value === null || $value instanceof Instant)  {
            return $value;
        }

        $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);

        return Instant::of(
            $dateTime->getTimestamp()
        );
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMappedDatabaseTypes(
        AbstractPlatform $platform,
    ): array {
        return [self::NAME];
    }

    public function requiresSQLCommentHint(
        AbstractPlatform $platform,
    ): bool {
        return true;
    }
}
