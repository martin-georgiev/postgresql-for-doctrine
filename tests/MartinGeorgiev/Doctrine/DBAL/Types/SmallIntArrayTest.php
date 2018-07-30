<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\SmallIntArray;

class SmallIntArrayTest extends AbstractIntegerArrayTest
{
    protected function setUp()
    {
        $this->fixture = $this->getMockBuilder(SmallIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('smallint[]', $this->fixture->getName());
    }

    /**
     * @return array
     */
    public function invalidTransformations()
    {
        return array_merge(parent::invalidTransformations(), [['-32767.01'], [-32769]]);
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                '$phpValue' => -32768,
                '$postgresValue' => '-32768',
            ],
            [
                '$phpValue' => 32767,
                '$postgresValue' => '32767',
            ],
        ];
    }
}
