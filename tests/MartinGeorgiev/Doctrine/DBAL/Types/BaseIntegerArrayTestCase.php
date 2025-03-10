<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseIntegerArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseIntegerArrayTestCase extends TestCase
{
    /**
     * @var BaseIntegerArray&MockObject
     */
    protected BaseIntegerArray $fixture;

    /**
     * @test
     *
     * @dataProvider provideInvalidTransformations
     */
    public function can_detect_invalid_for_transformation_php_value(mixed $phpValue): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return list<mixed>
     */
    public static function provideInvalidTransformations(): array
    {
        return [
            [true],
            [null],
            [-0.1],
            ['string'],
            [[]],
            [new \stdClass()],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_from_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider provideValidTransformations
     */
    public function can_transform_to_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }

    /**
     * @return list<array<string, int|string>>
     */
    abstract public static function provideValidTransformations(): array;
}
