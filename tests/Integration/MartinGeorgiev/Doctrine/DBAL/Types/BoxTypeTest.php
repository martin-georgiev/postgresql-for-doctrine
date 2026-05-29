<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BoxTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'box';
    }

    #[Test]
    public function roundtrips_null_value(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function normalizes_coordinate_order(BoxValueObject $inputValue, BoxValueObject $expectedValue): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($typeName, $columnType, $inputValue, $expectedValue);
    }

    /**
     * @return array<string, array{BoxValueObject, BoxValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'unit box at origin' => [
                BoxValueObject::fromString('(0,0),(1,1)'),
                BoxValueObject::fromString('(1,1),(0,0)'),
            ],
            'box with floats' => [
                BoxValueObject::fromString('(1.5,2.5),(3.5,4.5)'),
                BoxValueObject::fromString('(3.5,4.5),(1.5,2.5)'),
            ],
            'box with negative coordinates' => [
                BoxValueObject::fromString('(-3,-4),(-1,-2)'),
                BoxValueObject::fromString('(-1,-2),(-3,-4)'),
            ],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidBoxForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '(0,0),(1,1)');
    }
}
