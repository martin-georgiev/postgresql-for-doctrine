<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLqueryForDatabaseException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LqueryTypeTest extends ScalarTypeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('ltree');
    }

    protected function getTypeName(): string
    {
        return 'lquery';
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
     * Values are given in the form PostgreSQL stores them, so that the round-trip is stable.
     *
     * @return array<string, array{string}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single label' => ['Top'],
            'label followed by star' => ['Top.*'],
            'star only' => ['*'],
            'quantified star' => ['*{1,2}'],
            'open-ended quantified star' => ['*{,2}'],
            'quantified label' => ['tennis{1,}'],
            'negated alternatives' => ['!football|tennis'],
            'label modifiers' => ['foo%@*'],
            'hyphenated and underscored labels' => ['a-b.c_d'],
            'numeric labels' => ['1.2.3'],
            'full featured pattern' => ['Top.*{1,2}.sport@*.!football|tennis'],
        ];
    }

    #[Test]
    public function normalizes_modifier_order(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, 'sport*@', 'sport@*');
    }

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidLqueryForDatabaseException::class);

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
            'consecutive dots' => ['Top..Child'],
            'negated star' => ['!*'],
            'non-string value' => [42],
        ];
    }
}
