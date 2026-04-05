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
    #[DataProvider('provideGithubIssue482TestCases')]
    #[Test]
    public function can_handle_array_values(array $arrayValue): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $arrayValue);
    }

    public static function provideValidTransformations(): array
    {
        return [
            'simple text array' => [
                ['foo', 'bar', 'baz'],
            ],
            'text array with special chars' => [
                ['foo"bar', 'baz\qux', 'with,comma'],
            ],
            'text array with empty strings' => [
                ['', 'not empty', ''],
            ],
            'text array with unicode' => [
                ['café', 'naïve', 'résumé'],
            ],
            'text array with numbers as strings' => [
                ['123', '456', '789'],
            ],
            'text array with null element as string' => [
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
                ['1', 'test'],
            ],
            'mixed values' => [
                ['1', '2.5', '3.14', 'test', 'true', ''],
            ],
            'boolean values' => [
                ['1', '', '1', ''],
            ],
            'null values' => [
                ['', 'null', 'NULL'],
            ],
        ];
    }

    /**
     * This test scenarios specifically verify the fix for GitHub issue #482
     * where decimal strings with trailing zeros (e.g., "502.00", "505.00") were
     * being truncated to "502" and "505" when round-tripping through the database.
     * PostgreSQL returns these unquoted as {502.00,505.00}, and the fix ensures
     * they are preserved as strings with trailing zeros intact.
     */
    public static function provideGithubIssue482TestCases(): array
    {
        return [
            'mixed decimal formats' => [
                ['42.00', '123.50', '0.00', '999.99', '1.0', '2.000'],
            ],
            'decimal zero variations' => [
                ['0.0', '0.00', '0.000'],
            ],
        ];
    }
}
