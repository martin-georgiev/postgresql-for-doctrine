<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidPathForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Path as PathValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class PathTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'path';
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
    public function roundtrips_value(PathValueObject $pathValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $pathValueObject);
    }

    /**
     * @return array<string, array{PathValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'open path' => [PathValueObject::fromString('[(0,0),(1,1),(2,0)]')],
            'closed path' => [PathValueObject::fromString('((0,0),(1,1),(2,0))')],
            'path with floats' => [PathValueObject::fromString('[(1.5,2.5),(3.5,4.5)]')],
            'path with negative coordinates' => [PathValueObject::fromString('[(-1,-2),(-3,-4)]')],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidPathForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '[(0,0),(1,1)]');
    }
}
