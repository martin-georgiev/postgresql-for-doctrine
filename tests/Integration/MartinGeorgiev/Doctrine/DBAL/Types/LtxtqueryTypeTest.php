<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtxtqueryForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LtxtqueryTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ltree');
    }

    protected function getTypeName(): string
    {
        return 'ltxtquery';
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
            'single word' => ['Earth'],
            'conjunction' => ['Earth & Moon'],
            'disjunction' => ['Earth | Mars'],
            'negation' => ['!Transportation'],
            'parenthesised expression' => ['( a | b ) & c'],
            'word modifiers' => ['Moon%@*'],
            'full featured query' => ['Earth & Moon@* & !Transportation'],
            'hyphenated and underscored words' => ['a-b & c_d'],
        ];
    }

    #[Test]
    public function normalizes_operator_spacing(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, 'Earth&Moon', 'Earth & Moon');
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidLtxtqueryForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $value);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideInvalidValues(): array
    {
        return [
            'empty string' => [''],
            'dotted path' => ['a.b'],
            'unbalanced parenthesis' => ['(a'],
            'non-string value' => [42],
        ];
    }
}
