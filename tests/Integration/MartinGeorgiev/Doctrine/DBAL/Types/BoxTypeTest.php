<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BoxTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'box';
    }

    protected function getPostgresTypeName(): string
    {
        return 'BOX';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_box_values(string $testName, BoxValueObject $inputValue, BoxValueObject $expectedValue): void
    {
        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue(
            $this->getTypeName(),
            $this->getPostgresTypeName(),
            $inputValue,
            $expectedValue
        );
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(BoxValueObject::class, $expected);
        $this->assertInstanceOf(BoxValueObject::class, $actual);
        $this->assertSame($expected->__toString(), $actual->__toString(), \sprintf('Type %s round-trip failed', $typeName));
    }

    /**
     * @return array<string, array{string, BoxValueObject, BoxValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'unit box at origin' => [
                'unit box at origin',
                new BoxValueObject('(0,0),(1,1)'),
                new BoxValueObject('(1,1),(0,0)'),
            ],
            'box with floats' => [
                'box with floats',
                new BoxValueObject('(1.5,2.5),(3.5,4.5)'),
                new BoxValueObject('(3.5,4.5),(1.5,2.5)'),
            ],
            'box with negative coordinates' => [
                'box with negative coordinates',
                new BoxValueObject('(-3,-4),(-1,-2)'),
                new BoxValueObject('(-1,-2),(-3,-4)'),
            ],
        ];
    }
}
