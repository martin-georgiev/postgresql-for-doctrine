<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLtreeArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree as LtreeValueObject;
use PHPUnit\Framework\Attributes\Test;

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
     * @return array<string, array{array<int, LtreeValueObject|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple ltree array' => [[
                new LtreeValueObject(['foo', 'bar', 'baz']),
                new LtreeValueObject(['root', 'child']),
            ]],
            'numeric ltree array' => [[
                new LtreeValueObject(['1', '2', '3']),
                new LtreeValueObject(['4', '5']),
            ]],
            'single element ltree array' => [[
                new LtreeValueObject(['root']),
            ]],
            'ltree array with null item' => [[
                new LtreeValueObject(['foo', 'bar']),
                null,
                new LtreeValueObject(['baz']),
            ]],
            'empty ltree array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_ltree_instance(): void
    {
        $this->expectException(InvalidLtreeArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['Top.Sports']);
    }

    #[Test]
    public function rejects_integer_instead_of_ltree_instance(): void
    {
        $this->expectException(InvalidLtreeArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), [123]);
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
