<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CircleTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'circle';
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_circle_values(CircleValueObject $circleValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $circleValueObject);
    }

    /**
     * @return array<string, array{CircleValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'unit circle at origin' => [CircleValueObject::fromString('<(0,0),1>')],
            'circle with floats' => [CircleValueObject::fromString('<(1.5,2.5),3.5>')],
            'circle with negative center' => [CircleValueObject::fromString('<(-10,-20),5>')],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidCircleForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '<(0,0),1>');
    }
}
