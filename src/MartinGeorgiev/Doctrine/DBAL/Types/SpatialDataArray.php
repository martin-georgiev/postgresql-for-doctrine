<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\ConversionException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidWktSpatialDataException;
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

        $wktItems = [];
        $nestedBracketDepth = 0;
        $currentWktItem = '';
        $contentLength = \strlen($arrayContentWithoutBraces);

        for ($charIndex = 0; $charIndex < $contentLength; $charIndex++) {
            $currentChar = $arrayContentWithoutBraces[$charIndex];

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

    /**
     * Validates that an array item is suitable for database storage.
     *
     * For WKT spatial data arrays, items must be WktSpatialData instances.
     */
    public function isValidArrayItemForDatabase(mixed $item): bool
    {
        return $item instanceof WktSpatialData;
    }

    /**
     * Transforms PostgreSQL array item to a PHP compatible array item.
     */
    public function transformArrayItemForPHP(mixed $item): ?WktSpatialData
    {
        if ($item === null) {
            return null;
        }

        if (!\is_string($item)) {
            throw $this->createInvalidTypeExceptionForPHP($item);
        }

        try {
            return WktSpatialData::fromWkt($item);
        } catch (InvalidWktSpatialDataException) {
            throw $this->createInvalidFormatExceptionForPHP($item);
        }
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
