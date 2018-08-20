<?php

namespace MartinGeorgiev\Tests\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\TextArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TextArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform|MockObject
     */
    private $platform;

    /**
     * @var TextArray|MockObject
     */
    private $fixture;

    protected function setUp()
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(TextArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return array
     */
    public function validTransformations()
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => [
                    'some text here',
                    'and some here',
                    'even here there is text',
                ],
                'postgresValue' => '{"some text here","and some here","even here there is text"}',
            ],
        ];
    }

    /**
     * @test
     */
    public function has_name()
    {
        $this->assertEquals('text[]', $this->fixture->getName());
    }

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_from_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**

    /**
     * @test
     * @dataProvider validTransformations
     *
     * @param array|null $phpValue
     * @param string|null $postgresValue
     */
    public function can_transform_to_php_value($phpValue, $postgresValue)
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
