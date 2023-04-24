<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Termyn\Currencies;
use Termyn\Currency;

final class CurrencyType extends Type
{
    public const NAME = 'termyn.currency';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL([
            'length' => '3',
            'fixed' => true,
        ]);
    }

    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform
    ): ?string {
        if (! $value instanceof Currency) {
            return null;
        }

        return sprintf('%s', $value);
    }

    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?Currency {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return Currencies::from($value);
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
