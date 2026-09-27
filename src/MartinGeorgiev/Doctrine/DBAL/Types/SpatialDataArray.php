<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\SpatialColumnOptionsSQLDeclarationTrait;
use MartinGeorgiev\Doctrine\DBAL\Types\Traits\SpatialDataReadTrait;
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
    use SpatialColumnOptionsSQLDeclarationTrait;
    use SpatialDataReadTrait;

    /**
     * Rebuilds the array from each element's EWKT, the form the scalar spatial types read.
     *
     * An ARRAY() subquery over a NULL column yields '{}', so a NULL column is kept NULL explicitly.
     * A subquery guarantees no row order of its own, so WITH ORDINALITY pins the elements to their stored order.
     *
     * @param non-empty-string $sqlExpr
     * @param AbstractPlatform $platform
     */
    public function convertToPHPValueSQL($sqlExpr, $platform): string
    {
        return \sprintf(
            'CASE WHEN %1$s IS NULL THEN NULL ELSE ARRAY(SELECT %2$s FROM unnest(%1$s) WITH ORDINALITY AS items(item, position) ORDER BY position) END',
            $sqlExpr,
            $this->selectAsEwkt('item')
        );
    }

    protected function transformArrayItemForPostgres(mixed $item): string
    {
        if ($item === null) {
            return 'NULL';
        }

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
            return $item; // @phpstan-ignore return.type
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
                $this->throwInvalidFormatExceptionForPHP($postgresArray);
            }
        }

        // Literals this library wrote itself are unquoted.
        // A WKT body's own commas need parenthesis-aware splitting that the shared transformer does not do.
        return $this->parseUnquotedWktArray($arrayContentWithoutBraces, $delimiter);
    }

    /**
     * @return array<int, string|null>
     */
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
            $this->throwInvalidFormatExceptionForPHP($content);
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
        return $this->readWktSpatialData($item);
    }
}
