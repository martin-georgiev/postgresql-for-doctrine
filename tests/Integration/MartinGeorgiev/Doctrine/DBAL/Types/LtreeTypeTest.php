<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LtreeTypeTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->createLtreeExtension();
    }

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

        $this->runTypeTest($typeName, $columnType, null);
    }

    #[Test]
    public function can_handle_string_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, 'root.child.grand-child');
    }

    /**
     * Override to handle Ltree value object comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!$expected instanceof LtreeValueObject && !\is_string($expected) || !$actual instanceof LtreeValueObject) {
            throw new \InvalidArgumentException('LtreeTypeTest expects Ltree value objects.');
        }

        $this->assertLtreeEquals($expected, $actual, $typeName);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_ltree_values(string $testName, LtreeValueObject $ltreeValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runTypeTest($typeName, $columnType, $ltreeValueObject);
    }

    /**
     * @return array<string, array{string, ?LtreeValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'ltree simple string' => ['ltree simple string', new LtreeValueObject(['foo', 'bar', 'baz'])],
            'ltree simple numeric' => ['ltree simple numeric', new LtreeValueObject(['1', '2', '3'])],
            'ltree single numeric' => ['ltree single numeric', new LtreeValueObject(['1'])],
            'ltree empty' => ['ltree empty', new LtreeValueObject([])],
        ];
    }

    private function assertLtreeEquals(LtreeValueObject|string $ltreeValueObject, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(LtreeValueObject::class, $actual, 'Failed asserting that value is a Ltree object for type '.$typeName);
        $this->assertSame((string) $ltreeValueObject, (string) $actual, 'Failed asserting that ltree string representations are identical for type '.$typeName);
    }

    private function createLtreeExtension(): void
    {
        $sql = 'CREATE EXTENSION IF NOT EXISTS ltree';
        $this->connection->executeStatement($sql);
    }
}
