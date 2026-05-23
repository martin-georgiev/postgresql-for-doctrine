<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLsegArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg as LsegValueObject;
use PHPUnit\Framework\Attributes\Test;

class LsegArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'lseg[]';
    }

    /**
     * @return array<string, array{array<int, LsegValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single segment' => [[
                LsegValueObject::fromString('[(0,0),(1,1)]'),
            ]],
            'multiple segments' => [[
                LsegValueObject::fromString('[(1.5,2.5),(3.5,4.5)]'),
                LsegValueObject::fromString('[(-1,-2),(-3,-4)]'),
            ]],
            'empty lseg array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidLsegArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['[(0,0),(1,1)]']);
    }
}
