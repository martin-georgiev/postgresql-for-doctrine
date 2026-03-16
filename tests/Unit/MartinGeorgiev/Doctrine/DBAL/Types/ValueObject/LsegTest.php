<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLsegException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LsegTest extends TestCase
{
    #[DataProvider('provideValidLsegStrings')]
    #[Test]
    public function can_create_from_string(string $value): void
    {
        $lseg = Lseg::fromString($value);
        $this->assertSame($value, (string) $lseg);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideValidLsegStrings(): iterable
    {
        yield 'with square brackets' => ['[(1,2),(3,4)]'];
        yield 'without brackets' => ['(1,2),(3,4)'];
        yield 'with floats' => ['[(1.5,2.5),(3.5,4.5)]'];
        yield 'with negative coordinates' => ['[(-1,-2),(-3,-4)]'];
        yield 'from origin' => ['[(0,0),(1,1)]'];
    }

    #[DataProvider('provideInvalidLsegStrings')]
    #[Test]
    public function throws_exception_for_invalid_format(string $value): void
    {
        $this->expectException(InvalidLsegException::class);
        Lseg::fromString($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidLsegStrings(): iterable
    {
        yield 'empty string' => [''];
        yield 'plain text' => ['not a lseg'];
        yield 'single point' => ['(1,2)'];
        yield 'circle format' => ['<(1,2),3>'];
        yield 'line format' => ['{1,2,3}'];
    }

    #[Test]
    public function preserves_string_representation(): void
    {
        $value = '[(1,2),(3,4)]';
        $lseg = new Lseg($value);
        $this->assertSame($value, (string) $lseg);
    }
}
