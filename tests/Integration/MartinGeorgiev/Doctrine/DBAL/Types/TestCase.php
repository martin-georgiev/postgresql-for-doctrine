<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Connection $connection;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'user' => 'postgres',
            'password' => 'postgres',
            'dbname' => 'postgres',
        ]);

        $this->createTestTableForDataType();
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement(\sprintf('DROP TABLE IF EXISTS %s', $this->getTableName()));
        $this->connection->close();
    }

    abstract protected function getTableName(): string;

    abstract protected function getColumnName(): string;

    abstract protected function getColumnType(): string;

    protected function createTestTableForDataType(): void
    {
        $this->connection->executeStatement(\sprintf(
            'CREATE TABLE %s (id SERIAL PRIMARY KEY, %s %s)',
            $this->getTableName(),
            $this->getColumnName(),
            $this->getColumnType()
        ));
    }

    protected function insertTestData(?string $value): int
    {
        $result = $this->connection->executeQuery(
            \sprintf('INSERT INTO %s (%s) VALUES (?) RETURNING id', $this->getTableName(), $this->getColumnName()),
            [$value]
        );

        $row = $result->fetchAssociative();
        if ($row === false || !isset($row['id']) || !\is_numeric($row['id'])) {
            throw new \RuntimeException('Failed to insert test data');
        }

        return (int) $row['id'];
    }

    protected function getTestData(int $id): mixed
    {
        $sql = \sprintf('SELECT %s FROM %s WHERE id = ?', $this->getColumnName(), $this->getTableName());
        $result = $this->connection->fetchOne($sql, [$id]);

        if ($result === null) {
            return null;
        }

        if (\str_starts_with((string) $result, '{') && \str_ends_with((string) $result, '}')) {
            // Remove the outer braces and split by comma
            $content = \substr((string) $result, 1, -1);
            if ($content === '') {
                return [];
            }

            // Split by comma, but handle quoted strings and nested structures
            $elements = [];
            $current = '';
            $inQuotes = false;
            $inJson = false;
            $braceCount = 0;

            for ($i = 0; $i < \strlen($content); $i++) {
                $char = $content[$i];

                if ($char === '"' && ($i === 0 || $content[$i - 1] !== '\\')) {
                    $inQuotes = !$inQuotes;
                } elseif ($char === '{' && !$inQuotes) {
                    $braceCount++;
                    $inJson = true;
                } elseif ($char === '}' && !$inQuotes) {
                    $braceCount--;
                    if ($braceCount === 0) {
                        $inJson = false;
                    }
                }

                if ($char === ',' && !$inQuotes && !$inJson) {
                    $elements[] = \trim($current);
                    $current = '';
                } else {
                    $current .= $char;
                }
            }

            if ($current !== '') {
                $elements[] = \trim($current);
            }

            // Convert elements to appropriate types
            $converted = \array_map(static function ($element) {
                // Handle JSON/JSONB arrays
                if (\str_starts_with($element, '"') && \str_ends_with($element, '"')) {
                    $json = \json_decode(\substr($element, 1, -1), true);
                    if (\json_last_error() === JSON_ERROR_NONE) {
                        return $json;
                    }
                }

                // Handle numeric values
                if (\is_numeric($element)) {
                    if (\str_contains($element, '.')) {
                        return (float) $element;
                    }

                    return (int) $element;
                }

                // Handle boolean values
                if ($element === 't' || $element === 'true') {
                    return true;
                }

                if ($element === 'f' || $element === 'false') {
                    return false;
                }

                // Handle point values
                if (\preg_match('/^\(([^,]+),([^)]+)\)$/', $element, $matches)) {
                    return new PointValueObject((float) $matches[1], (float) $matches[2]);
                }

                // Return as string for other cases
                return $element;
            }, $elements);

            // For array types, return the array as a whole
            if (\str_ends_with($this->getColumnName(), '[]')) {
                return $converted;
            }

            // For non-array types, return the first element
            return $converted[0] ?? null;
        }

        return $result;
    }
}
