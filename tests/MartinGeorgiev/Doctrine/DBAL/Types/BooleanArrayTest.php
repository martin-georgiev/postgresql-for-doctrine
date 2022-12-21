<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BooleanArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BooleanArrayTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    /**
     * @var BooleanArray&MockObject
     */
    private MockObject $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(BooleanArray::class)
            ->addMethods([])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function validTransformations(): array
    {
        return [
            [
                'phpValue' => null,
                'postgresValue' => null,
                'platformValue' => null,
            ],
            [
                'phpValue' => [],
                'postgresValue' => '{}',
                'platformValue' => [],
            ],
            [
                'phpValue' => [true, false, true],
                'postgresValue' => '{1,0,1}',
                'platformValue' => ['1', '0', '1'],
            ],
        ];
    }

    /**
     * @test
     */
    public function has_name(): void
    {
        $this->assertEquals('bool[]', $this->fixture->getName());
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_from_php_value(?array $phpValue, ?string $postgresValue, ?array $platformValue): void
    {
        $this->platform->method('convertBooleansToDatabaseValue')
            ->with($phpValue)
            ->willReturn($platformValue);

        $this->assertEquals($postgresValue, $this->fixture->convertToDatabaseValue($phpValue, $this->platform));
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_to_php_value(?array $phpValue, ?string $postgresValue): void
    {
        $this->platform->method('convertFromBoolean')
            ->with($this->anything())
            ->willReturn($this->returnCallback('boolval'));

        $this->assertEquals($phpValue, $this->fixture->convertToPHPValue($postgresValue, $this->platform));
    }
}
