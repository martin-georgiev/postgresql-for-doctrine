<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;

class CircleArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'circle[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'CIRCLE[]';
    }

    /**
     * @return array<string, array{string, array<int, CircleValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single circle' => ['single circle', [
                new CircleValueObject('<(0,0),1>'),
            ]],
            'multiple circles' => ['multiple circles', [
                new CircleValueObject('<(1.5,2.5),3.5>'),
                new CircleValueObject('<(-10,-20),5>'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(CircleValueObject::class, $expectedItem);
            $this->assertInstanceOf(CircleValueObject::class, $actual[$index]);
            $this->assertSame($expectedItem->__toString(), $actual[$index]->__toString());
        }
    }
}
