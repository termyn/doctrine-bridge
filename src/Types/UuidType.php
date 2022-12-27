<?php

declare(strict_types=1);

namespace Termyn\Bridge\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Termyn\Uuid;
use Termyn\Uuid\Ramsey\RamseyUuid;
use Termyn\Uuid\RegexUuidValidator;
use Termyn\Uuid\Symfony\SymfonyUuid;

abstract class UuidType extends Type
{
    public function getSQLDeclaration(
        array $column,
        AbstractPlatform $platform,
    ): string {
        return $this->hasNativeGuidType($platform)
            ? $platform->getGuidTypeDeclarationSQL($column)
            : $platform->getBinaryTypeDeclarationSQL([
                'length' => '16',
                'fixed' => true,
            ]);
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): string {
        if (! $value instanceof Uuid) {
            throw ConversionException::conversionFailedInvalidType(
                value: $value,
                toType: $this->getName(),
                possibleTypes: [
                    SymfonyUuid::class,
                    RamseyUuid::class,
                ]
            );
        }

        return $this->hasNativeGuidType($platform)
            ? $value->toString()
            : $value->toBinary();
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): Uuid {
        return is_string($value) && $this->isValidUuidString($value)
            ? $this->covertToUuid($value)
            : throw ConversionException::conversionFailedFormat(
                value: $value,
                toType: $this->getName(),
                expectedFormat: Uuid::NIL,
            );
    }

    public function getMappedDatabaseTypes(
        AbstractPlatform $platform,
    ): array {
        return [
            $this->getName(),
        ];
    }

    public function requiresSQLCommentHint(
        AbstractPlatform $platform,
    ): bool {
        return true;
    }

    abstract public function getName(): string;

    abstract protected function covertToUuid(string $uuid): Uuid;

    private function hasNativeGuidType(
        AbstractPlatform $platform,
    ): bool {
        $nativeGuidDeclaration = $platform->getGuidTypeDeclarationSQL([]);
        $stringGuidDeclaration = $platform->getStringTypeDeclarationSQL([
            'length' => 36,
            'fixed' => true,
        ]);

        return $nativeGuidDeclaration !== $stringGuidDeclaration;
    }

    private function isValidUuidString(string $uuid): bool
    {
        return (new RegexUuidValidator())->validate($uuid);
    }
}
