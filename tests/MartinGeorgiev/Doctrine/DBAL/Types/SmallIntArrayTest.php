<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;

class SmallIntArrayTest extends BaseIntegerArrayTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fixture = $this->getMockBuilder(SmallIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('smallint[]', $this->fixture->getName());
    }

    public function invalidTransformations(): array
    {
        return \array_merge(parent::invalidTransformations(), [['-32767.01'], [-32769]]);
    }

    /**
     * @return list<array{
     *     phpValue: int,
     *     postgresValue: string
     * }>
     */
    public function validTransformations(): array
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
