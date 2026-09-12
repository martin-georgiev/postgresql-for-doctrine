<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;
use MartinGeorgiev\Utils\Exception\InvalidArrayFormatException;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;

/**
 * Base class for PostgreSQL array types containing WKT/EWKT spatial data.
 *
 * This class provides specialized array parsing logic that understands the nested
 * structure of WKT geometries (parentheses, coordinate lists, etc.).
 * It splits array elements without breaking on commas inside coordinate groups.
 *
 * @since 3.5
 */
abstract class SpatialDataArray extends BaseArray
{
    /**
     * Get a regex pattern that matches all supported geometry types.
     *
     * This method dynamically builds the pattern from the GeometryType enum
     * to ensure consistency and eliminate duplication.
     */
    private function getGeometryTypesPattern(): string
    {
        $geometryTypes = \array_map(
            static fn (GeometryType $geometryType): string => $geometryType->value,
            GeometryType::cases()
        );

        return '('.\implode('|', $geometryTypes).')';
    }

    /**
     * Build dimensional modifier regex patterns for geometry type normalization.
     *
     * Uses the DimensionalModifier enum to ensure consistency and eliminate duplication.
     *
     * @return array<string, string> Array of regex pattern => replacement pairs
     */
    private function getDimensionalModifierPatterns(): array
    {
        $geometryTypesPattern = $this->getGeometryTypesPattern();
        $modifierValues = \array_map(
            static fn (DimensionalModifier $dimensionalModifier): string => $dimensionalModifier->value,
            DimensionalModifier::cases()
        );
        $modifiersPattern = '('.\implode('|', $modifierValues).')';

        return [
            // No-space variants: POINTZM/POINTZ/POINTM -> POINT ZM|Z|M (built from enum)
            \sprintf('/^%s%s\b/', $geometryTypesPattern, $modifiersPattern) => '$1 $2',
            // ST_AsText extra space format: POINT Z ( -> POINT Z(
            \sprintf('/^%s\s+%s\s+\(/', $geometryTypesPattern, $modifiersPattern) => '$1 $2(',
            // Multiple spaces: POINT  Z -> POINT Z
            \sprintf('/^%s\s+%s\b/', $geometryTypesPattern, $modifiersPattern) => '$1 $2',
        ];
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

        // A WKT body carries spaces and commas, and fromWkt() checks only the outer structure,
        // so it may also carry a quote or a backslash the array literal has to escape.
        return $this->quoteAndEscapeArrayItem((string) $this->getValidatedArrayItem($item));
    }

    /**
     * PostGIS records ':' as typdelim for geometry and geography.
     */
    protected function getArrayElementDelimiter(): string
    {
        return ':';
    }

    protected function getValidatedArrayItem(mixed $item): WktSpatialData
    {
        if ($this->isValidArrayItemForDatabase($item)) {
            return $item; // @phpstan-ignore-line
        }

        $this->throwInvalidItemException($item);
    }

    /**
     * Transforms a PostgreSQL array containing WKT/EWKT geometries to a PHP array.
     *
     * Examples:
     * - '{POINT(1 2),LINESTRING(0 0, 1 1)}' -> ['POINT(1 2)', 'LINESTRING(0 0, 1 1)']
     * - '{POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))}' -> ['POLYGON((0 0, 0 1, 1 1, 1 0, 0 0))']
     * - '{}' -> []
     */
    protected function transformPostgresArrayToPHPArray(string $postgresArray): array
    {
        $trimmedArray = \trim($postgresArray);
        if ($trimmedArray === '{}' || $trimmedArray === '') {
            return [];
        }

        $arrayContentWithoutBraces = \substr($trimmedArray, 1, -1);
        if ($arrayContentWithoutBraces === '') {
            return [];
        }

        // A WKT body never contains ':', the SRID prefix using ';', so its presence marks a literal
        // written with this type's own element delimiter rather than the ',' a text[] arrives with.
        $delimiter = \str_contains($arrayContentWithoutBraces, ':') ? $this->getArrayElementDelimiter() : ',';

        // Every WKT string contains a space, so PostgreSQL always quotes it.
        // Content carrying no quote and no WKT body is a list of bare NULL tokens.
        $isPostgresEmittedShape = \str_contains($arrayContentWithoutBraces, '"')
            || !\str_contains($arrayContentWithoutBraces, '(');
        if ($isPostgresEmittedShape) {
            try {
                return PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray(
                    $postgresArray,
                    preserveStringTypes: true,
                    delimiter: $delimiter
                );
            } catch (InvalidArrayFormatException) {
                throw $this->createInvalidFormatExceptionForPHP($postgresArray);
            }
        }

        // Literals this library wrote itself are unquoted.
        // A WKT body's own commas need parenthesis-aware splitting that the shared transformer does not do.
        return $this->parseUnquotedWktArray($arrayContentWithoutBraces, $delimiter);
    }

    private function parseUnquotedWktArray(string $content, string $delimiter): array
    {
        $wktItems = [];
        $nestedBracketDepth = 0;
        $currentWktItem = '';
        $contentLength = \strlen($content);

        for ($charIndex = 0; $charIndex < $contentLength; $charIndex++) {
            $currentChar = $content[$charIndex];

            // Track opening brackets/parentheses to handle nested WKT structures
            if ($currentChar === '(' || $currentChar === '{') {
                $nestedBracketDepth++;
                $currentWktItem .= $currentChar;

                continue;
            }

            // Track closing brackets/parentheses
            if ($currentChar === ')' || $currentChar === '}') {
                $nestedBracketDepth--;
                $currentWktItem .= $currentChar;

                continue;
            }

            // Only split at the top level, never inside WKT coordinate groups
            if ($currentChar === $delimiter && $nestedBracketDepth === 0) {
                $wktItems[] = $currentWktItem;
                $currentWktItem = '';

                continue;
            }

            $currentWktItem .= $currentChar;
        }

        // Content left after the final delimiter is the last element; nothing left means the
        // literal ended on a delimiter, which PostgreSQL rejects as a malformed array.
        if ($currentWktItem !== '') {
            $wktItems[] = $currentWktItem;
        } elseif ($wktItems !== []) {
            throw $this->createInvalidFormatExceptionForPHP($content);
        }

        return \array_map(
            static fn (string $item): ?string => \trim($item) === 'NULL' ? null : \trim($item),
            $wktItems
        );
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item === null || $item instanceof WktSpatialData;
    }

    public function transformArrayItemForPHP(mixed $item): ?WktSpatialData
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw $this->createInvalidTypeExceptionForPHP($item);
        }

        try {
            $normalizedWkt = $this->normalizePostgreSQLDimensionalModifiers($item);

            return WktSpatialData::fromWkt($normalizedWkt);
        } catch (InvalidWktSpatialDataException) {
            throw $this->createInvalidFormatExceptionForPHP($item);
        }
    }

    /**
     * Normalize PostgreSQL dimensional modifier format to standard WKT format.
     *
     * PostgreSQL can return dimensional modifiers in different formats:
     * - ST_AsEWKT(): POINTZ, POINTM, POINTZM (no spaces)
     * - ST_AsText(): POINT Z, POINT M, POINT ZM (with spaces)
     * - Hybrid approach: SRID=4326;POINT Z (1 2 3) (SRID + extra space)
     */
    private function normalizePostgreSQLDimensionalModifiers(string $wkt): string
    {
        // Handle SRID prefix if present
        $sridPrefix = '';
        $hasSrid = \str_starts_with($wkt, 'SRID=');
        if ($hasSrid) {
            $sridSeparatorPosition = \strpos($wkt, ';');
            if ($sridSeparatorPosition === false) {
                throw InvalidWktSpatialDataException::forMissingSemicolonInEwkt();
            }

            $sridPrefix = \substr($wkt, 0, $sridSeparatorPosition + 1);
            $wkt = \substr($wkt, $sridSeparatorPosition + 1);
        }

        // Normalize dimensional modifiers using patterns built from WktGeometryType enum
        foreach ($this->getDimensionalModifierPatterns() as $pattern => $replacement) {
            $wkt = \preg_replace($pattern, $replacement, (string) $wkt);
        }

        return $sridPrefix.$wkt;
    }

    /**
     * Creates an exception for invalid type during PHP conversion.
     * Subclasses should override this to provide specific exception types.
     */
    abstract protected function createInvalidTypeExceptionForPHP(mixed $item): ConversionException;

    /**
     * Creates an exception for invalid format during PHP conversion.
     * Subclasses should override this to provide specific exception types.
     */
    abstract protected function createInvalidFormatExceptionForPHP(mixed $item): ConversionException;
}
