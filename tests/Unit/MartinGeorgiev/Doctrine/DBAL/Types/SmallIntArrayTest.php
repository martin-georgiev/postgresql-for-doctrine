<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;
use PHPUnit\Framework\Attributes\Test;

final class SmallIntArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        $this->fixture = new SmallIntArray();
    }

    #[Test]
    public function has_name(): void
    {
        $this->assertSame('smallint[]', $this->fixture->getName());
    }

    public static function provideInvalidDatabaseValueInputs(): array
    {
        return \array_merge(parent::provideInvalidDatabaseValueInputs(), [
            'greater than max smallint' => ['32768'],
            'less than min smallint' => ['-32769'],
            'scientific notation' => ['1.23e4'],
            'decimal number' => ['123.45'],
        ]);
    }

    /**
     * @return list<array{
     *     postgresValue: string,
     *     expectedValue: int
     * }>
     */
    public static function provideValidItemTransformationsToPHP(): array
    {
        return [
            [
                'expectedValue' => 32767,
                'postgresValue' => '32767',
            ],
            [
                'expectedValue' => -32768,
                'postgresValue' => '-32768',
            ],
            [
                'expectedValue' => 0,
                'postgresValue' => '0',
            ],
            [
                'expectedValue' => 1,
                'postgresValue' => '1',
            ],
            [
                'expectedValue' => -1,
                'postgresValue' => '-1',
            ],
            [
                'expectedValue' => 9999,
                'postgresValue' => '9999',
            ],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideOutOfRangeValues(): array
    {
        return [
            'above max smallint' => ['32768'],
            'below min smallint' => ['-32769'],
        ];
    }
}
