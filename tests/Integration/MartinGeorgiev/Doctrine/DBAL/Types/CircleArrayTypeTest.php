<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidCircleArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Circle as CircleValueObject;
use PHPUnit\Framework\Attributes\Test;

final class CircleArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'circle[]';
    }

    /**
     * @return array<string, array{array<int, CircleValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single circle' => [[
                CircleValueObject::fromString('<(0,0),1>'),
            ]],
            'multiple circles' => [[
                CircleValueObject::fromString('<(1.5,2.5),3.5>'),
                CircleValueObject::fromString('<(-10,-20),5>'),
            ]],
            'empty circle array' => [[]],
        ];
    }

    #[Test]
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidCircleArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['<(0,0),1>']);
    }
}
