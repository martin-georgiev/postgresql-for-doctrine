<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Cube;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidCubeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CubeTest extends TestCase
{
    #[DataProvider('provideFromStringTestCases')]
    #[Test]
    public function parses_from_string(string $input, string $expectedStringRepresentation): void
    {
        $cube = Cube::fromString($input);

        $this->assertSame($expectedStringRepresentation, (string) $cube);
    }

    /**
     * @return array<string, array{input: string, expectedStringRepresentation: string}>
     */
    public static function provideFromStringTestCases(): array
    {
        return [
            'point' => [
                'input' => '(1, 2, 3)',
                'expectedStringRepresentation' => '(1, 2, 3)',
            ],
            'box' => [
                'input' => '(1, 2, 3),(4, 5, 6)',
                'expectedStringRepresentation' => '(1, 2, 3),(4, 5, 6)',
            ],
            'one-dimensional point' => [
                'input' => '(1)',
                'expectedStringRepresentation' => '(1)',
            ],
            'bare one-dimensional point' => [
                'input' => '1',
                'expectedStringRepresentation' => '(1)',
            ],
            'bare coordinate list' => [
                'input' => '1,2,3',
                'expectedStringRepresentation' => '(1, 2, 3)',
            ],
            'bracketed box' => [
                'input' => '[(1,2),(3,4)]',
                'expectedStringRepresentation' => '(1, 2),(3, 4)',
            ],
            'negative coordinates' => [
                'input' => '(-1.5, -2.25)',
                'expectedStringRepresentation' => '(-1.5, -2.25)',
            ],
            'exponent notation' => [
                'input' => '(1e3, 2)',
                'expectedStringRepresentation' => '(1000, 2)',
            ],
            'surrounding whitespace' => [
                'input' => '  ( 1 , 2 ) ',
                'expectedStringRepresentation' => '(1, 2)',
            ],
            'trailing zeroes' => [
                'input' => '(1.0, 2.0)',
                'expectedStringRepresentation' => '(1, 2)',
            ],
        ];
    }

    #[Test]
    public function normalizes_zero_volume_box_to_point(): void
    {
        $cube = new Cube([1.0, 2.0, 3.0], [1.0, 2.0, 3.0]);

        $this->assertTrue($cube->isPoint());
        $this->assertNull($cube->getSecondCorner());
        $this->assertSame('(1, 2, 3)', (string) $cube);
    }

    #[Test]
    public function preserves_corner_order(): void
    {
        $cube = new Cube([4.0, 5.0, 6.0], [1.0, 2.0, 3.0]);

        $this->assertSame([4.0, 5.0, 6.0], $cube->getFirstCorner());
        $this->assertSame([1.0, 2.0, 3.0], $cube->getSecondCorner());
        $this->assertSame('(4, 5, 6),(1, 2, 3)', (string) $cube);
    }

    #[Test]
    public function returns_correct_dimensions(): void
    {
        $this->assertSame(1, Cube::point(1.0)->getDimensions());
        $this->assertSame(3, Cube::point(1.0, 2.0, 3.0)->getDimensions());
        $this->assertSame(2, (new Cube([1.0, 2.0], [3.0, 4.0]))->getDimensions());
    }

    #[Test]
    public function creates_point_from_variadic_coordinates(): void
    {
        $cube = Cube::point(1.0, 2.0, 3.0);

        $this->assertTrue($cube->isPoint());
        $this->assertSame('(1, 2, 3)', (string) $cube);
    }

    #[Test]
    public function preserves_full_float_precision(): void
    {
        $coordinate = 0.12345678901234568;

        $cube = Cube::point($coordinate);

        $this->assertSame('(0.12345678901234568)', (string) $cube);
        $this->assertSame([$coordinate], Cube::fromString((string) $cube)->getFirstCorner());
    }

    #[DataProvider('provideRoundtripTestCases')]
    #[Test]
    public function roundtrips_string_representation(Cube $cube): void
    {
        $this->assertEquals($cube, Cube::fromString((string) $cube));
    }

    /**
     * @return array<string, array{Cube}>
     */
    public static function provideRoundtripTestCases(): array
    {
        return [
            'point' => [Cube::point(1.0, 2.0, 3.0)],
            'box' => [new Cube([1.0, 2.0], [3.0, 4.0])],
            'one-dimensional point' => [Cube::point(42.0)],
            'negative coordinates' => [new Cube([-1.5, -2.5], [-0.5, -0.25])],
            'very large coordinate' => [Cube::point(1.0E+300)],
            'very small coordinate' => [Cube::point(1.0E-10)],
            'high precision coordinate' => [Cube::point(0.12345678901234568, 1.0)],
        ];
    }

    #[DataProvider('provideInvalidStringRepresentations')]
    #[Test]
    public function throws_exception_for_invalid_string_representations(string $value): void
    {
        $this->expectException(InvalidCubeException::class);

        Cube::fromString($value);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidStringRepresentations(): array
    {
        return [
            'empty string' => [''],
            'empty parentheses' => ['()'],
            'unclosed parenthesis' => ['(1,2'],
            'non-numeric coordinate' => ['(a,b)'],
            'three corners' => ['(1,2),(3,4),(5,6)'],
            'not a cube' => ['not a cube'],
            'NaN coordinate' => ['(NaN, 1)'],
            'infinite coordinate' => ['(Infinity, 1)'],
            'wrong separator' => ['(1;2)'],
        ];
    }

    #[Test]
    public function throws_exception_for_empty_coordinates(): void
    {
        $this->expectException(InvalidCubeException::class);

        new Cube([]);
    }

    #[Test]
    public function throws_exception_for_mismatched_dimensions(): void
    {
        $this->expectException(InvalidCubeException::class);

        new Cube([1.0, 2.0], [3.0]);
    }

    #[DataProvider('provideNonFiniteCoordinates')]
    #[Test]
    public function throws_exception_for_non_finite_coordinates(float $coordinate): void
    {
        $this->expectException(InvalidCubeException::class);

        Cube::point($coordinate);
    }

    /**
     * @return array<string, array{float}>
     */
    public static function provideNonFiniteCoordinates(): array
    {
        return [
            'NaN' => [NAN],
            'positive infinity' => [INF],
            'negative infinity' => [-INF],
        ];
    }
}
