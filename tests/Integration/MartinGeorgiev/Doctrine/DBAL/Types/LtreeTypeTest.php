<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LtreeTypeTest extends TestCase
{
    use LtreeAssertionTrait;

    protected function getTypeName(): string
    {
        return 'ltree';
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

    #[DataProvider('provideInvalidValues')]
    #[Test]
    public function rejects_invalid_value(mixed $value): void
    {
        $this->expectException(InvalidLtreeForDatabaseException::class);

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
            'consecutive dots produce an empty label' => ['root..child'],
            'non-string value' => [42],
        ];
    }
}
