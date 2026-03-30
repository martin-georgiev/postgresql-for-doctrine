<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;

class LsegArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'lseg[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LSEG[]';
    }

    /**
     * @return array<string, array{string, array<int, LsegValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single segment' => ['single segment', [
                new LsegValueObject('[(0,0),(1,1)]'),
            ]],
            'multiple segments' => ['multiple segments', [
                new LsegValueObject('[(1.5,2.5),(3.5,4.5)]'),
                new LsegValueObject('[(-1,-2),(-3,-4)]'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(LsegValueObject::class, $expectedItem);
            $this->assertInstanceOf(LsegValueObject::class, $actual[$index]);
            $this->assertSame($expectedItem->__toString(), $actual[$index]->__toString());
        }
    }
}
