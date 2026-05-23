<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidBoxArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Box as BoxValueObject;
use PHPUnit\Framework\Attributes\Test;

class BoxArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'box[]';
    }

    /**
     * @return array<string, array{array<int, BoxValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single box' => [[
                BoxValueObject::fromString('(3,4),(1,2)'),
            ]],
            'multiple boxes' => [[
                BoxValueObject::fromString('(1,1),(0,0)'),
                BoxValueObject::fromString('(-1,-2),(-3,-4)'),
            ]],
            'empty box array' => [[]],
        ];
    }

    #[Test]
    public function rejects_raw_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidBoxArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['(1,1),(0,0)']);
    }
}
