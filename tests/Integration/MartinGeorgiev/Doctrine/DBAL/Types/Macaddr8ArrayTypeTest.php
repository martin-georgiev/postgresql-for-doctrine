<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidMacaddr8ArrayItemForPHPException;
use PHPUnit\Framework\Attributes\Test;

final class Macaddr8ArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'macaddr8[]';
    }

    /**
     * @return array<string, array{array<int, string|null>}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'simple macaddr8 array' => [['08:00:2b:ff:fe:01:02:03', '00:0c:29:aa:bb:cc:dd:ee']],
            'macaddr8 array with zeros' => [['00:00:00:00:00:00:00:00', 'ff:ff:ff:ff:ff:ff:ff:ff']],
            'macaddr8 array with single value' => [['08:00:2b:ff:fe:01:02:03']],
            'empty macaddr8 array' => [[]],
            'macaddr8 array with null item' => [['08:00:2b:ff:fe:01:02:03', null, '00:0c:29:aa:bb:cc:dd:ee']],
        ];
    }

    #[Test]
    public function rejects_invalid_address_item(): void
    {
        $this->expectException(InvalidMacaddr8ArrayItemForPHPException::class);

        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, ['invalid-mac8', '08:00:2b:ff:fe:01:02:03']);
    }
}
