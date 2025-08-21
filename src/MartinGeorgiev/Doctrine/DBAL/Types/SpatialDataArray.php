<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DimensionalModifier;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\GeometryType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\WktSpatialData;

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
            // Handle no-space format (POINTZM -> POINT ZM, POINTZ -> POINT Z, POINTM -> POINT M)
            \sprintf('/^%sZM\b/', $geometryTypesPattern) => '$1 ZM',
            \sprintf('/^%sZ\b/', $geometryTypesPattern) => '$1 Z',
            \sprintf('/^%sM\b/', $geometryTypesPattern) => '$1 M',
            // Handle ST_AsText extra space format (POINT Z (1 2 3) -> POINT Z(1 2 3))
            \sprintf('/^%s\s+%s\s+\(/', $geometryTypesPattern, $modifiersPattern) => '$1 $2(',
            // Handle multiple spaces (POINT  Z -> POINT Z)
            \sprintf('/^%s\s+%s\b/', $geometryTypesPattern, $modifiersPattern) => '$1 $2',
        ];
    }

    protected function getValidatedArrayItem(mixed $item): WktSpatialData
    {
        if ($this->isValidArrayItemForDatabase($item)) {
            return $item; // @phpstan-ignore-line
        }

        throw $this->createInvalidTypeExceptionForPHP($item);
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

        // Handle quoted array format: {"item1","item2","item3"}
        $isQuotedArray = \str_starts_with($trimmedArray, '{"') && \str_ends_with($trimmedArray, '"}');
        if ($isQuotedArray) {
            $arrayContentWithoutBraces = \substr($trimmedArray, 2, -2);
            if ($arrayContentWithoutBraces === '') {
                return [];
            }

            return $this->parseQuotedWktArray($arrayContentWithoutBraces);
        }

        // Handle unquoted array format: {item1,item2,item3} (fallback for backward compatibility)
        $arrayContentWithoutBraces = \substr($trimmedArray, 1, -1);
        if ($arrayContentWithoutBraces === '') {
            return [];
        }

        return $this->parseUnquotedWktArray($arrayContentWithoutBraces);
    }

    private function parseQuotedWktArray(string $content): array
    {
        $wktItems = [];
        $currentWktItem = '';
        $nestedBracketDepth = 0;
        $contentLength = \strlen($content);
        $charIndex = 0;

        while ($charIndex < $contentLength) {
            $currentChar = $content[$charIndex];

            // Track nested parentheses within the quoted WKT
            if ($currentChar === '(') {
                $nestedBracketDepth++;
                $currentWktItem .= $currentChar;
            } elseif ($currentChar === ')') {
                $nestedBracketDepth--;
                $currentWktItem .= $currentChar;
            } elseif ($currentChar === '"' && $nestedBracketDepth === 0) {
                // Found end quote at top level - this ends the current item
                if ($currentWktItem !== '') {
                    $wktItems[] = $currentWktItem;
                    $currentWktItem = '';
                }

                // Skip the quote and comma separator: ","
                $charIndex++; // Skip the quote
                if ($charIndex < $contentLength && $content[$charIndex] === ',') {
                    $charIndex++; // Skip the comma
                }

                if ($charIndex < $contentLength && $content[$charIndex] === '"') {
                    $charIndex++; // Skip the opening quote of next item
                }

                continue;
            } else {
                $currentWktItem .= $currentChar;
            }

            $charIndex++;
        }

        // Add the last item if there's content
        if ($currentWktItem !== '') {
            $wktItems[] = $currentWktItem;
        }

        return $wktItems;
    }

    private function parseUnquotedWktArray(string $content): array
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

            // Only split on commas at the top level (not inside WKT coordinate groups)
            if ($currentChar === ',' && $nestedBracketDepth === 0) {
                $wktItems[] = $currentWktItem;
                $currentWktItem = '';

                continue;
            }

            $currentWktItem .= $currentChar;
        }

        // Add the last WKT item if there's content
        if ($currentWktItem !== '') {
            $wktItems[] = $currentWktItem;
        }

        return \array_map('trim', $wktItems);
    }

    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item instanceof WktSpatialData;
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
     *
     * This method normalizes all formats to the standard WKT format using
     * patterns dynamically built from the GeometryType and DimensionalModifier enums.
     */
    private function normalizePostgreSQLDimensionalModifiers(string $wkt): string
    {
        // Handle SRID prefix if present
        $sridPrefix = '';
        $hasSrid = \str_starts_with($wkt, 'SRID=');
        if ($hasSrid) {
            $sridEnd = \strpos($wkt, ';');
            if ($sridEnd === false) {
                throw new \RuntimeException();
            }

            $sridPrefix = \substr($wkt, 0, $sridEnd + 1);
            $wkt = \substr($wkt, $sridEnd + 1);
        }

        // Normalize dimensional modifiers using patterns built from WktGeometryType enum
        $patterns = $this->getDimensionalModifierPatterns();

        foreach ($patterns as $pattern => $replacement) {
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
