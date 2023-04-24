<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Dbal\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Termyn\Uuid;
use Termyn\Uuid\Ramsey\RamseyUuid;
use Termyn\Uuid\RegexUuidValidator;
use Termyn\Uuid\Symfony\SymfonyUuid;

abstract class UuidsType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getClobTypeDeclarationSQL($column);
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform
    ): ?string {
        if (! is_array($value) || count($value) === 0) {
            return null;
        }

        $uuids = array_map(
            function ($uuid): string {
                $uuid = ($uuid instanceof Uuid) ? $uuid : throw ConversionException::conversionFailedInvalidType(
                    value: $uuid,
                    toType: $this->getName(),
                    possibleTypes: [
                        SymfonyUuid::class,
                        RamseyUuid::class,
                    ],
                );

                return sprintf('%s', $uuid);
            },
            $value,
        );

        return implode(',', $uuids);
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): array {
        if (! is_string($value)) {
            return [];
        }

        return array_map(
            function (string $uuid): Uuid {
                return $this->isValidUuidString($uuid)
                    ? $this->covertToUuid($uuid)
                    : throw ConversionException::conversionFailedFormat($uuid, $this->getName(), Uuid::NIL);
            },
            explode(',', $value),
        );
    }

    abstract public function getName(): string;

    abstract protected function covertToUuid(string $uuid): Uuid;

    private function isValidUuidString(string $uuid): bool
    {
        return (new RegexUuidValidator())->validate($uuid);
    }
}
