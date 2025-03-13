<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;

class SmallIntArrayTest extends BaseIntegerArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = new SmallIntArray(); // @phpstan-ignore-line
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        self::assertEquals('smallint[]', $this->fixture->getName());
    }

    public static function provideInvalidTransformations(): array
    {
        return \array_merge(parent::provideInvalidTransformations(), [['-32767.01'], [-32769]]);
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
                'phpValue' => -32768,
                'postgresValue' => '-32768',
            ],
            [
                'phpValue' => 32767,
                'postgresValue' => '32767',
            ],
        ];
    }
}
