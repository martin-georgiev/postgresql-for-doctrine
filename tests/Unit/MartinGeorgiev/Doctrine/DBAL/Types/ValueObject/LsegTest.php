<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidLsegException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Lseg;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LsegTest extends TestCase
{
    #[DataProvider('provideValidLsegStrings')]
    #[Test]
    public function can_create_from_string(string $input, string $expectedOutput): void
    {
        $lseg = Lseg::fromString($input);
        $this->assertSame($expectedOutput, (string) $lseg);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideValidLsegStrings(): iterable
    {
        yield 'with square brackets' => ['[(1,2),(3,4)]', '[(1,2),(3,4)]'];
        yield 'without brackets' => ['(1,2),(3,4)', '[(1,2),(3,4)]'];
        yield 'with floats' => ['[(1.5,2.5),(3.5,4.5)]', '[(1.5,2.5),(3.5,4.5)]'];
        yield 'with negative coordinates' => ['[(-1,-2),(-3,-4)]', '[(-1,-2),(-3,-4)]'];
        yield 'from origin' => ['[(0,0),(1,1)]', '[(0,0),(1,1)]'];
        yield 'with whitespace inside point parentheses' => ['[( 1 , 2 ),( 3 , 4 )]', '[(1,2),(3,4)]'];
        yield 'with whitespace after opening bracket' => ['[ (1,2),(3,4)]', '[(1,2),(3,4)]'];
        yield 'with whitespace before closing bracket' => ['[(1,2),(3,4) ]', '[(1,2),(3,4)]'];
        yield 'with whitespace around comma between points' => ['[(1,2) , (3,4)]', '[(1,2),(3,4)]'];
        yield 'with mixed whitespace variations' => ['[ ( 1 , 2 ) , ( 3 , 4 ) ]', '[(1,2),(3,4)]'];
    }

    #[Test]
    public function getters_return_point_values(): void
    {
        $lseg = Lseg::fromString('[(1.5,-2.5),(3,4)]');
        $this->assertSame(1.5, $lseg->getStart()->getX());
        $this->assertSame(-2.5, $lseg->getStart()->getY());
        $this->assertSame(3.0, $lseg->getEnd()->getX());
        $this->assertSame(4.0, $lseg->getEnd()->getY());
    }

    #[Test]
    public function can_construct_from_points(): void
    {
        $lseg = new Lseg(new Point(1.5, -2.5), new Point(3.0, 4.0));
        $this->assertSame('[(1.5,-2.5),(3,4)]', (string) $lseg);
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
        yield 'mismatched opening bracket only' => ['[(1,2),(3,4)'];
        yield 'mismatched closing bracket only' => ['(1,2),(3,4)]'];
    }

    #[Test]
    public function normalizes_bracketless_input_to_bracketed_output(): void
    {
        $lseg = Lseg::fromString('(1,2),(3,4)');
        $this->assertSame('[(1,2),(3,4)]', (string) $lseg);
    }
}
