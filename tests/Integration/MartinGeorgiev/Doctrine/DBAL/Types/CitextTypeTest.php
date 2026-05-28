<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CitextTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('citext');
    }

    protected function getTypeName(): string
    {
        return 'citext';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function roundtrips_value(string $testValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $testValue);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple text' => ['Hello World'],
            'mixed case' => ['CaseInsensitive TEXT'],
            'empty string' => [''],
        ];
    }
}
