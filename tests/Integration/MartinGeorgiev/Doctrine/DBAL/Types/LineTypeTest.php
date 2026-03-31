<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LineTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'line';
    }

    protected function getPostgresTypeName(): string
    {
        return 'LINE';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(LineValueObject::class, $expected);
        $this->assertInstanceOf(LineValueObject::class, $actual);
        $this->assertSame($expected->__toString(), $actual->__toString(), \sprintf('Type %s round-trip failed', $typeName));
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_line_values(string $testName, LineValueObject $lineValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $lineValueObject);
    }

    /**
     * @return array<string, array{string, LineValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple line' => ['simple line', LineValueObject::fromString('{1,0,0}')],
            'line with floats' => ['line with floats', LineValueObject::fromString('{1.5,2.5,3.5}')],
            'line with negative coefficients' => ['line with negative coefficients', LineValueObject::fromString('{-1,-2,-3}')],
        ];
    }
}
