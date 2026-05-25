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

    public static function provideValidTransformations(): array
    {
        return [
            'simple string array' => [['foo', 'bar', 'baz']],
            'mixed case array' => [['Hello', 'WORLD', 'CamelCase']],
            'array with special chars' => [['café', 'naïve']],
            'array with null item' => [[null, 'hello']],
            'array with empty string' => [['', 'hello']],
        ];
    }
}
