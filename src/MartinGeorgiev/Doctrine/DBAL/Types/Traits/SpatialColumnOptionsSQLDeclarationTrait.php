<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSpatialColumnDeclarationException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;

/**
 * Adds option-aware SQL declaration for PostGIS types that support a type modifier.
 * Generates `TYPE(subtype,srid)` from the `geometry_type` and `srid` column options, otherwise bare `TYPE`.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait SpatialColumnOptionsSQLDeclarationTrait
{
    /**
     * @var string
     */
    private const ANY_GEOMETRY_SUBTYPE = 'GEOMETRY';

    /**
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $geometryType = $this->findGeometryTypeOption($fieldDeclaration);
        $srid = $this->findSridOption($fieldDeclaration);
        $typeName = \strtoupper(static::TYPE_NAME);

        if ($geometryType === null && $srid === null) {
            return $typeName;
        }

        $subtype = $geometryType ?? self::ANY_GEOMETRY_SUBTYPE;
        if ($srid === null) {
            return \sprintf('%s(%s)', $typeName, $subtype);
        }

        return \sprintf('%s(%s,%d)', $typeName, $subtype, $srid);
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     */
    private function findGeometryTypeOption(array $fieldDeclaration): ?string
    {
        $geometryType = $fieldDeclaration['geometry_type'] ?? null;
        if ($geometryType === null) {
            return null;
        }

        if (!\is_string($geometryType)) {
            throw InvalidSpatialColumnDeclarationException::forInvalidGeometryType($geometryType);
        }

        $normalised = \strtoupper(\trim($geometryType));
        if (!$this->isSupportedSubtype($normalised)) {
            throw InvalidSpatialColumnDeclarationException::forInvalidGeometryType($geometryType);
        }

        return $normalised;
    }

    private function isSupportedSubtype(string $subtype): bool
    {
        return $subtype === self::ANY_GEOMETRY_SUBTYPE
            || GeometryType::tryFrom($subtype) !== null
            || $this->isGeometryTypeWithDimensionalModifier($subtype);
    }

    /**
     * PostGIS spells a dimensional variant by appending the modifier to the base type, so POINTZM is POINT plus ZM.
     */
    private function isGeometryTypeWithDimensionalModifier(string $subtype): bool
    {
        foreach ($this->getDimensionalModifiersSortedWithLongestFirst() as $modifier) {
            if (\str_ends_with($subtype, $modifier)) {
                return GeometryType::tryFrom(\substr($subtype, 0, -\strlen($modifier))) !== null;
            }
        }

        return false;
    }

    /**
     * @return list<string> ZM before M, so that POINTZM is split into POINT plus ZM rather than POINTZ plus M
     */
    private function getDimensionalModifiersSortedWithLongestFirst(): array
    {
        $modifiers = \array_map(
            static fn (DimensionalModifier $dimensionalModifier): string => $dimensionalModifier->value,
            DimensionalModifier::cases()
        );
        \usort($modifiers, static fn (string $a, string $b): int => \strlen($b) <=> \strlen($a));

        return $modifiers;
    }

    /**
     * @param array<string, mixed> $fieldDeclaration
     *
     * @throws InvalidSpatialColumnDeclarationException
     */
    private function findSridOption(array $fieldDeclaration): ?int
    {
        $srid = $fieldDeclaration['srid'] ?? null;
        if ($srid === null) {
            return null;
        }

        if (\is_string($srid) && \ctype_digit($srid)) {
            return (int) $srid;
        }

        if (\is_int($srid) && $srid >= 0) {
            return $srid;
        }

        throw InvalidSpatialColumnDeclarationException::forInvalidSrid($srid);
    }
}
