<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Termyn\Currencies;
use Termyn\Money;

final class MoneyType extends Type
{
    public const NAME = 'termyn.money';

    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform
    ): string {
        return $platform->getBinaryTypeDeclarationSQL([
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

        return sprintf('%s%s', $value->currency->symbol(), $value->amount);
    }

    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?Money {
        $value = parent::convertToPHPValue($value, $platform);
        if (! is_string($value)) {
            return null;
        }

        preg_match('/^(\-|\+)?([^0-9\-\+]{1,3})([1-9]+[0-9\.]*)$/', $value, $matches);
        if (! is_array($matches) || count($matches) == 4) {
            return null;
        }

        $symbol = sprintf('%s', $matches[2]);
        $amount = floatval(
            sprintf('%s%s', $matches[1], $matches[3])
        );

        return Money::of($amount, Currencies::fromSymbol($symbol));
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
