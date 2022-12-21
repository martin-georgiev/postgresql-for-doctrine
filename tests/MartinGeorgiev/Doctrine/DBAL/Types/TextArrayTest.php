<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

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
     * @var MockObject|TextArray
     */
    private $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(TextArray::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
            ],
            [
                'phpValue' => [
                    1,
                    '2',
                    'text',
                    'some text here',
                    'and some here',
                    <<<'END'
''"quotes"'' ain't no """worry""", '''right''' Alexander O'Vechkin?
END
                    ,
                    'back-slashing\double-slashing\\\hooking though',
                    'and "double-quotes"',
                ],
                'postgresValue' => <<<'END'
{1,2,"text","some text here","and some here","''\"quotes\"'' ain't no \"\"\"worry\"\"\", '''right''' Alexander O'Vechkin?","back-slashing\\double-slashing\\\\hooking though","and \"double-quotes\""}
END
                ,
            ],
        ];
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('text[]', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
