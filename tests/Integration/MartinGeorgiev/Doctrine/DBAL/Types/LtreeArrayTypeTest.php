<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;

class LtreeArrayTypeTest extends ArrayTypeTestCase
{
    use LtreeAssertionTrait;

    protected function getTypeName(): string
    {
        return 'ltree[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LTREE[]';
    }

    /**
     * @return array<string, array{string, array<int, LtreeValueObject|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple ltree array' => ['simple ltree array', [
                new LtreeValueObject(['foo', 'bar', 'baz']),
                new LtreeValueObject(['root', 'child']),
            ]],
            'numeric ltree array' => ['numeric ltree array', [
                new LtreeValueObject(['1', '2', '3']),
                new LtreeValueObject(['4', '5']),
            ]],
            'single element ltree array' => ['single element ltree array', [
                new LtreeValueObject(['root']),
            ]],
            'empty ltree array' => ['empty ltree array', []],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('LtreeArray count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            if ($expectedItem === null) {
                $this->assertNull($actual[$index]);
            } else {
                $this->assertInstanceOf(LtreeValueObject::class, $expectedItem);
                $this->assertLtreeEquals($expectedItem, $actual[$index], $typeName);
            }
        }
    }
}
