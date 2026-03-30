<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;

class PathArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'path[]';
    }

    protected function getPostgresTypeName(): string
    {
        return 'PATH[]';
    }

    /**
     * @return array<string, array{string, array<int, PathValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single open path' => ['single open path', [
                new PathValueObject('[(0,0),(1,1),(2,0)]'),
            ]],
            'open and closed paths' => ['open and closed paths', [
                new PathValueObject('[(1.5,2.5),(3.5,4.5)]'),
                new PathValueObject('((0,0),(1,1),(2,0))'),
            ]],
        ];
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertIsArray($expected);
        $this->assertIsArray($actual);
        $this->assertCount(\count($expected), $actual, \sprintf('Array count mismatch for type %s', $typeName));

        foreach ($expected as $index => $expectedItem) {
            $this->assertInstanceOf(PathValueObject::class, $expectedItem);
            $this->assertInstanceOf(PathValueObject::class, $actual[$index]);
            $this->assertSame($expectedItem->__toString(), $actual[$index]->__toString());
        }
    }
}
