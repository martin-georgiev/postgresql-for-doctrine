<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidLineArrayItemForDatabaseException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Line as LineValueObject;
use PHPUnit\Framework\Attributes\Test;

final class LineArrayTypeTest extends ArrayTypeTestCase
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
    public function rejects_string_instead_of_value_object(): void
    {
        $this->expectException(InvalidLineArrayItemForDatabaseException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['{1,0,0}']);
    }
}
