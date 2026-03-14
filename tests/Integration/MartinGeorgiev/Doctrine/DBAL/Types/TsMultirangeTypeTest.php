<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsMultirange as TsMultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\TsRange;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class TsMultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'tsmultirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'TSMULTIRANGE';
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_multirange_value(TsMultirangeVO $tsMultirangeVO): void
    {
        $typeName = $this->getTypeName();
        $columnType = $this->getPostgresTypeName();

        $this->runDbalBindingRoundTrip($typeName, $columnType, $tsMultirangeVO);
    }

    /**
     * @return array<string, array{TsMultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new TsMultirangeVO([])],
            'single timestamp range' => [
                new TsMultirangeVO([new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00'))]),
            ],
            'two non-overlapping timestamp ranges' => [
                new TsMultirangeVO([
                    new TsRange(new \DateTimeImmutable('2024-01-01 09:00:00'), new \DateTimeImmutable('2024-01-01 12:00:00')),
                    new TsRange(new \DateTimeImmutable('2024-01-01 14:00:00'), new \DateTimeImmutable('2024-01-01 17:00:00')),
                ]),
            ],
        ];
    }
}
