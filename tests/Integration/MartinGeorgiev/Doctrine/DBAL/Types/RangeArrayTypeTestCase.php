<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Range as RangeValueObject;

/**
 * @template R of RangeValueObject
 */
abstract class RangeArrayTypeTestCase extends ArrayTypeTestCase
{
    /**
     * @return class-string<R>
     */
    abstract protected static function getRangeValueObjectClass(): string;

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array type %s round-trip count mismatch', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $actualItem = $actual[$index];
            if ($expectedItem === null) {
                $this->assertNull($actualItem, \sprintf('Expected null at index %d for type %s', $index, $typeName));

                continue;
            }

            $this->assertInstanceOf(static::getRangeValueObjectClass(), $expectedItem);
            $this->assertInstanceOf(static::getRangeValueObjectClass(), $actualItem);
            $this->assertEquals(
                $expectedItem->__toString(),
                $actualItem->__toString(),
                \sprintf('Range string representation mismatch at index %d for type %s', $index, $typeName)
            );
        }
    }
}
