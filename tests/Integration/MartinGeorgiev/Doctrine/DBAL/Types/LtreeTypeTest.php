<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LtreeTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'ltree';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LTREE';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[Test]
    public function can_handle_string_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, 'root.child.grand-child');
    }

    /**
     * Override to handle Ltree value object comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$actual instanceof LtreeValueObject) {
            throw new \InvalidArgumentException('LtreeTypeTest expects actual value to be a Ltree object');
        }

        if (!$expected instanceof LtreeValueObject && !\is_string($expected)) {
            throw new \InvalidArgumentException('LtreeTypeTest expects expected value to be a Ltree object or string');
        }

        $this->assertLtreeEquals($expected, $actual, $typeName);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_ltree_values(LtreeValueObject $ltreeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $ltreeValueObject);
    }

    /**
     * @return array<string, array{LtreeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'ltree simple string' => [new LtreeValueObject(['foo', 'bar', 'baz'])],
            'ltree simple numeric' => [new LtreeValueObject(['1', '2', '3'])],
            'ltree single numeric' => [new LtreeValueObject(['1'])],
            'ltree empty' => [new LtreeValueObject([])],
        ];
    }

    private function assertLtreeEquals(LtreeValueObject|string $ltreeValueObject, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(LtreeValueObject::class, $actual, 'Failed asserting that value is a Ltree object for type '.$typeName);
        $this->assertSame((string) $ltreeValueObject, (string) $actual, 'Failed asserting that ltree string representations are identical for type '.$typeName);
    }
}
