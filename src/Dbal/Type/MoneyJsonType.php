<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Termyn\Currencies;
use Termyn\Money;

final class MoneyJsonType extends JsonType
{
    public const NAME = 'termyn.money_json';

    public function convertToDatabaseValue(
        $value,
        AbstractPlatform $platform,
    ): ?string {
        if (! $value instanceof Money) {
            return null;
        }

        return parent::convertToDatabaseValue([
            $value->amount,
            $value->currency->code(),
        ], $platform);
    }

    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): ?Money {
        $value = parent::convertToPHPValue($value, $platform);
        if (! is_array($value) || count($value) < 2) {
            return null;
        }

        return Money::of(
            amount: floatval($value[0]),
            currency: Currencies::fromCode($value[1]),
        );
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
