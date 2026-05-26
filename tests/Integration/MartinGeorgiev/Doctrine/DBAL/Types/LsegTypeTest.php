<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class LsegTypeTest extends TestCase
{
    protected function getTypeName(): string
    {
        return 'lseg';
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
    public function roundtrips_value(LsegValueObject $lsegValueObject): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $lsegValueObject);
    }

    /**
     * @return array<string, array{LsegValueObject}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple segment' => [LsegValueObject::fromString('[(0,0),(1,1)]')],
            'segment with floats' => [LsegValueObject::fromString('[(1.5,2.5),(3.5,4.5)]')],
            'segment with negative coordinates' => [LsegValueObject::fromString('[(-1,-2),(-3,-4)]')],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidLsegForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, '[(0,0),(1,1)]');
    }
}
