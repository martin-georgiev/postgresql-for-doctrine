<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

final class CitextArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('citext');
    }

    protected function getTypeName(): string
    {
        return 'citext[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple string array' => [['foo', 'bar', 'baz']],
            'mixed case array' => [['Hello', 'WORLD', 'CamelCase']],
            'array with special chars' => [['café', 'naïve']],
            'array with null item' => [[null, 'hello']],
            'array with empty string' => [['', 'hello']],
            'array with numeric-looking values' => [['123', '2.5', '0', '1e3']],
            'array with boolean-looking values' => [['t', 'f', 'true', 'false']],
            'array with null-looking values' => [['null', 'NULL']],
        ];
    }
}
