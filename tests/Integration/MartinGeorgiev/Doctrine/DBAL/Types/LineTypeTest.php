<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LineTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'line';
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
    public function roundtrips_value(LineValueObject $lineValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $lineValueObject);
    }

    /**
     * @return array<string, array{LineValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple line' => [LineValueObject::fromString('{1,0,0}')],
            'line with floats' => [LineValueObject::fromString('{1.5,2.5,3.5}')],
            'line with negative coefficients' => [LineValueObject::fromString('{-1,-2,-3}')],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidLineForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '{1,0,0}');
    }
}
