<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Termyn\Currencies;
use Termyn\InvalidMoneyString;
use Termyn\Money;

final class MoneyType extends Type
{
    public const NAME = 'termyn.money';

    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform
    ): string {
        return $platform->getStringTypeDeclarationSQL([
            'length' => '16',
            'fixed' => false,
        ]);
    }

    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform,
    ): ?string {
        if (! $value instanceof Money) {
            return null;
        }

        return (string) $value;
    }

    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?Money {
        $value = parent::convertToPHPValue($value, $platform);
        if (! is_string($value)) {
            return null;
        }

        try {
            return Money::from($value);
        } catch (InvalidMoneyString) {
            throw ConversionException::conversionFailedFormat($value, Money::class, '[-]($|€|*)number');
        }
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return [self::NAME];
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
