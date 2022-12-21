<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseIntegerArray;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class BaseIntegerArrayTest extends TestCase
{
    /**
     * @var BaseIntegerArray|MockObject
     */
    protected $fixture;

    /**
     * @return array<int, mixed>
     */
    public function invalidTransformations(): array
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
     * @dataProvider invalidTransformations
     *
     * @param mixed $phpValue
     */
    public function can_detect_invalid_for_transformation_php_value($phpValue): void
    {
        $this->assertFalse($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    abstract public function validTransformations(): array;

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_from_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertTrue($this->fixture->isValidArrayItemForDatabase($phpValue));
    }

    /**
     * @test
     *
     * @dataProvider validTransformations
     */
    public function can_transform_to_php_value(int $phpValue, string $postgresValue): void
    {
        $this->assertEquals($phpValue, $this->fixture->transformArrayItemForPHP($postgresValue));
    }
}
