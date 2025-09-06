<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TextArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'text[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TEXT[]';
    }

    #[DataProvider('provideValidTransformations')]
    #[DataProvider('provideGithubIssue424TestCases')]
    #[Test]
    public function can_handle_array_values(string $testName, array $arrayValue): void
    {
        parent::can_handle_array_values($testName, $arrayValue);
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple text array' => [
                'simple text array',
                ['foo', 'bar', 'baz'],
            ],
            'text array with special chars' => [
                'text array with special chars',
                ['foo"bar', 'baz\qux', 'with,comma'],
            ],
            'text array with empty strings' => [
                'text array with empty strings',
                ['', 'not empty', ''],
            ],
            'text array with unicode' => [
                'text array with unicode',
                ['café', 'naïve', 'résumé'],
            ],
            'text array with numbers as strings' => [
                'text array with numbers as strings',
                ['123', '456', '789'],
            ],
            'text array with null element as string' => [
                'text array with null elements',
                ['foo', 'null', 'baz'],
            ],
        ];
    }

    /**
     * This test scenarios specifically verify the scenarios from GitHub issue #424
     * where PostgreSQL optimizes {"1","test"} to {1,test} and we have to ensure
     * that TextArray correctly preserves string types when converted back for PHP.
     */
    public static function provideGithubIssue424TestCases(): array
    {
        return [
            'numeric values' => [
                'Numeric values should be preserved as strings',
                ['1', 'test'],
            ],
            'mixed values' => [
                'Mixed numeric values should be preserved as strings',
                ['1', '2.5', '3.14', 'test', 'true', ''],
            ],
            'boolean values' => [
                'Boolean values should be converted to strings',
                ['1', '', '1', ''],
            ],
            'null values' => [
                'Null values should be converted to strings',
                ['', 'null', 'NULL'],
            ],
        ];
    }
}
