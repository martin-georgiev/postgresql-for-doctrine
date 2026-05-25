<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPointArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point as PointValueObject;
use PHPUnit\Framework\Attributes\Test;

final class PointArrayTypeTest extends ArrayTypeTestCase
{
    use PointAssertionTrait;

    protected function getTypeName(): string
    {
        return 'point[]';
    }

    /**
     * @return array<string, array{array<int, PointValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple point array' => [[
                new PointValueObject(1.23, 4.56),
                new PointValueObject(-10.5, -20.75),
            ]],
            'point array with zero coordinates' => [[
                new PointValueObject(0.0, 0.0),
                new PointValueObject(100.0, 200.0),
            ]],
            'point array with high precision' => [[
                new PointValueObject(123.456789, -987.654321),
                new PointValueObject(0.123456, 0.987654),
            ]],
            'point array with integer coordinates' => [[
                new PointValueObject(100, 200),
                new PointValueObject(-50, -100),
            ]],
            'empty point array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPointArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['(1.0,2.0)']);
    }

    /**
     * Override to handle Point array-specific coordinate precision comparison.
     */
    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        if (!\is_array($expected) || !\is_array($actual)) {
            throw new \InvalidArgumentException('PointArrayTypeTest expects arrays of Point value objects.');
        }

        if (!$this->isPointArray($expected) || !$this->isPointArray($actual)) {
            throw new \InvalidArgumentException('PointArrayTypeTest expects arrays containing only Point value objects.');
        }

        $this->assertPointArrayEquals($expected, $actual, $typeName);
    }

    /**
     * Assert that two point arrays are equal with coordinate precision.
     */
    protected function assertPointArrayEquals(array $expected, array $actual, string $typeName): void
    {
        $this->assertCount(\count($expected), $actual, \sprintf('Point array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedPoint) {
            if ($expectedPoint instanceof PointValueObject && $actual[$index] instanceof PointValueObject) {
                $this->assertPointEquals($expectedPoint, $actual[$index], $typeName);
            }
        }
    }
}
