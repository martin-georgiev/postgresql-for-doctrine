<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CircleTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'circle';
    }

    protected function getPostgresTypeName(): string
    {
        return 'CIRCLE';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(CircleValueObject::class, $actual);
        $this->assertSame((string) $expected, (string) $actual, \sprintf('Type %s round-trip failed', $typeName));
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_circle_values(string $testName, CircleValueObject $circleValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $circleValueObject);
    }

    /**
     * @return array<string, array{string, CircleValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'unit circle at origin' => ['unit circle at origin', new CircleValueObject('<(0,0),1>')],
            'circle with floats' => ['circle with floats', new CircleValueObject('<(1.5,2.5),3.5>')],
            'circle with negative center' => ['circle with negative center', new CircleValueObject('<(-10,-20),5>')],
        ];
    }
}
