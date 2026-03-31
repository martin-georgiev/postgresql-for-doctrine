<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;

class LineArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'line[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LINE[]';
    }

    /**
     * @return array<string, array{string, array<int, LineValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single line' => ['single line', [
                LineValueObject::fromString('{1,0,0}'),
            ]],
            'multiple lines' => ['multiple lines', [
                LineValueObject::fromString('{1.5,2.5,3.5}'),
                LineValueObject::fromString('{-1,-2,-3}'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(LineValueObject::class, $expectedItem);
            $this->assertInstanceOf(LineValueObject::class, $actual[$index]);
            $this->assertSame($expectedItem->__toString(), $actual[$index]->__toString());
        }
    }
}
