<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PathTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'path';
    }

    protected function getPostgresTypeName(): string
    {
        return 'PATH';
    }

    protected function assertTypeValueEquals(mixed $expected, mixed $actual, string $typeName): void
    {
        $this->assertInstanceOf(PathValueObject::class, $actual);
        $this->assertSame((string) $expected, (string) $actual, \sprintf('Type %s round-trip failed', $typeName));
    }

    #[Test]
    public function can_handle_null_values(): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), null);
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_handle_path_values(string $testName, PathValueObject $pathValueObject): void
    {
        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), $pathValueObject);
    }

    /**
     * @return array<string, array{string, PathValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'open path' => ['open path', new PathValueObject('[(0,0),(1,1),(2,0)]')],
            'closed path' => ['closed path', new PathValueObject('((0,0),(1,1),(2,0))')],
            'path with floats' => ['path with floats', new PathValueObject('[(1.5,2.5),(3.5,4.5)]')],
            'path with negative coordinates' => ['path with negative coordinates', new PathValueObject('[(-1,-2),(-3,-4)]')],
        ];
    }
}
