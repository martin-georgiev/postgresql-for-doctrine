<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;

class BoxArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'box[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BOX[]';
    }

    /**
     * @return array<string, array{string, array<int, BoxValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single box' => ['single box', [
                BoxValueObject::fromString('(1,2),(3,4)'),
            ]],
            'multiple boxes' => ['multiple boxes', [
                BoxValueObject::fromString('(0,0),(1,1)'),
                BoxValueObject::fromString('(-1,-2),(-3,-4)'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($actual as $item) {
            $this->assertInstanceOf(BoxValueObject::class, $item);
        }
    }
}
