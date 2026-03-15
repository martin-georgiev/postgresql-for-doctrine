<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Multirange as Int4MultirangeVO;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range;
use PHPUnit\Framework\Attributes\Test;

class Int4MultirangeTypeTest extends MultirangeTypeTestCase
{
    protected function getTypeName(): string
    {
        return 'int4multirange';
    }

    protected function getPostgresTypeName(): string
    {
        return 'INT4MULTIRANGE';
    }

    /**
     * @return array<string, array{Int4MultirangeVO}>
     */
    public static function provideValidTransformations(): array
    {
        return [
            'empty multirange' => [new Int4MultirangeVO([])],
            'single range' => [new Int4MultirangeVO([new Int4Range(1, 10)])],
            'two non-overlapping ranges' => [
                new Int4MultirangeVO([new Int4Range(1, 5), new Int4Range(10, 20)]),
            ],
        ];
    }

    #[Test]
    public function can_normalizes_exclusive_lower_bound_to_inclusive(): void
    {
        $input = new Int4MultirangeVO([new Int4Range(1, 10, false, false)]);
        $expected = new Int4MultirangeVO([new Int4Range(2, 10)]);

        $this->runDbalBindingRoundTripExpectingDifferentRetrievedValue($this->getTypeName(), $this->getPostgresTypeName(), $input, $expected);
    }
}
