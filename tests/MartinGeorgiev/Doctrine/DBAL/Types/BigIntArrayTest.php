<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BigIntArray;

class BigIntArrayTest extends AbstractIntegerArrayTest
{
    protected function setUp()
    {
        $this->fixture = $this->getMockBuilder(BigIntArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('bigint[]', $this->fixture->getName());
    }

    /**
     * @return array
     */
    public function invalidTransformations()
    {
        return array_merge(parent::invalidTransformations(), [['-9223372036854775807.01'], [-9223372036854775809]]);
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                'phpValue' => -9223372036854775807,
                'postgresValue' => '-9223372036854775807',
            ],
            [
                'phpValue' => 9223372036854775807,
                'postgresValue' => '9223372036854775807',
            ],
        ];
    }
}
