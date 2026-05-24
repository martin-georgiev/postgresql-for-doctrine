<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CitextArrayTypeTest extends ArrayTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        try {
            $this->connection->executeStatement('CREATE EXTENSION IF NOT EXISTS citext');
        } catch (\Throwable) {
            $this->markTestSkipped('citext extension is not available');
        }
    }

    protected function getTypeName(): string
    {
        return 'citext[]';
    }

    /**
     * @return array<string, array{array<int, string>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple string array' => [['foo', 'bar', 'baz']],
            'mixed case array' => [['Hello', 'WORLD', 'CamelCase']],
            'array with special chars' => [['café', 'naïve']],
        ];
    }
}
