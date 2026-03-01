<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;
use PHPUnit\Framework\Attributes\Test;

class SmallIntArrayTest extends BaseIntegerArrayTestCase
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
     *     phpValue: int,
     *     postgresValue: string
     * }>
     */
    public static function provideValidTransformations(): array
    {
        return [
            [
                'phpValue' => 32767,
                'postgresValue' => '32767',
            ],
            [
                'phpValue' => -32768,
                'postgresValue' => '-32768',
            ],
            [
                'phpValue' => 0,
                'postgresValue' => '0',
            ],
            [
                'phpValue' => 1,
                'postgresValue' => '1',
            ],
            [
                'phpValue' => -1,
                'postgresValue' => '-1',
            ],
            [
                'phpValue' => 9999,
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
