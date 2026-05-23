<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\Test;

class LineArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'line[]';
    }

    /**
     * @return array<string, array{array<int, LineValueObject>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'single line' => [[
                LineValueObject::fromString('{1,0,0}'),
            ]],
            'multiple lines' => [[
                LineValueObject::fromString('{1.5,2.5,3.5}'),
                LineValueObject::fromString('{-1,-2,-3}'),
            ]],
            'empty line array' => [[]],
        ];
    }

    #[Test]
    public function rejects_raw_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidLineArrayItemForDatabaseException::class);

        $this->runDbalBindingRoundTrip($this->getTypeName(), $this->getPostgresTypeName(), ['{1,0,0}']);
    }
}
