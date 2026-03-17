<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidSparsevecForPHPException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions\InvalidSparsevecException;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Sparsevec;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SparsevecTest extends TestCase
{
    #[Test]
    public function can_be_constructed_with_elements_and_dimensions(): void
    {
        $sparsevec = new Sparsevec([1 => 1.5, 3 => 2.0], 5);
        $this->assertSame([1 => 1.5, 3 => 2.0], $sparsevec->getElements());
        $this->assertSame(5, $sparsevec->getDimensions());
    }

    #[Test]
    public function can_be_constructed_with_no_elements(): void
    {
        $sparsevec = new Sparsevec([], 3);
        $this->assertSame([], $sparsevec->getElements());
        $this->assertSame(3, $sparsevec->getDimensions());
    }

    #[DataProvider('provideToStringCases')]
    #[Test]
    public function can_convert_to_string(Sparsevec $sparsevec, string $expected): void
    {
        $this->assertSame($expected, (string) $sparsevec);
    }

    /**
     * @return array<string, array{sparsevec: Sparsevec, expected: string}>
     */
    public static function provideToStringCases(): array
    {
        return [
            'single non-zero element' => [
                'sparsevec' => new Sparsevec([2 => 0.5], 4),
                'expected' => '{2:0.5}/4',
            ],
            'multiple non-zero elements' => [
                'sparsevec' => new Sparsevec([1 => 1.5, 3 => 2.0], 5),
                'expected' => '{1:1.5,3:2}/5',
            ],
            'empty elements' => [
                'sparsevec' => new Sparsevec([], 3),
                'expected' => '{}/3',
            ],
        ];
    }

    #[DataProvider('provideFromStringCases')]
    #[Test]
    public function can_be_parsed_from_string(string $input, array $expectedElements, int $expectedDimensions): void
    {
        $sparsevec = Sparsevec::fromString($input);
        $this->assertSame($expectedElements, $sparsevec->getElements());
        $this->assertSame($expectedDimensions, $sparsevec->getDimensions());
    }

    /**
     * @return array<string, array{input: string, expectedElements: array<int, float>, expectedDimensions: int}>
     */
    public static function provideFromStringCases(): array
    {
        return [
            'single element' => [
                'input' => '{2:0.5}/4',
                'expectedElements' => [2 => 0.5],
                'expectedDimensions' => 4,
            ],
            'multiple elements' => [
                'input' => '{1:1.5,3:2.0}/5',
                'expectedElements' => [1 => 1.5, 3 => 2.0],
                'expectedDimensions' => 5,
            ],
            'empty elements' => [
                'input' => '{}/3',
                'expectedElements' => [],
                'expectedDimensions' => 3,
            ],
            'negative value' => [
                'input' => '{1:-0.5}/3',
                'expectedElements' => [1 => -0.5],
                'expectedDimensions' => 3,
            ],
        ];
    }

    #[DataProvider('provideInvalidFromStringCases')]
    #[Test]
    public function throws_exception_for_invalid_format(string $input): void
    {
        $this->expectException(InvalidSparsevecForPHPException::class);
        Sparsevec::fromString($input);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidFromStringCases(): array
    {
        return [
            'missing dimensions' => ['{1:1.5}'],
            'missing braces' => ['1:1.5/5'],
            'non-numeric value' => ['{1:abc}/5'],
            'empty string' => [''],
            'wrong format' => ['[1,2,3]'],
            'zero dimensions' => ['{1:1.5}/0'],
            'index below range' => ['{0:1.5}/3'],
            'index above range' => ['{4:1.5}/3'],
        ];
    }

    #[DataProvider('provideInvalidConstructorInputs')]
    #[Test]
    public function throws_exception_for_invalid_constructor_input(array $elements, int $dimensions): void
    {
        $this->expectException(InvalidSparsevecException::class);
        new Sparsevec($elements, $dimensions); // @phpstan-ignore-line
    }

    /**
     * @return array<string, array{elements: array<int, mixed>, dimensions: int}>
     */
    public static function provideInvalidConstructorInputs(): array
    {
        return [
            'non-positive dimensions' => ['elements' => [1 => 1.0], 'dimensions' => 0],
            'key below range' => ['elements' => [0 => 1.0], 'dimensions' => 3],
            'key above range' => ['elements' => [4 => 1.0], 'dimensions' => 3],
            'non-numeric value' => ['elements' => [1 => 'not-a-number'], 'dimensions' => 3],
            'NAN value' => ['elements' => [1 => \NAN], 'dimensions' => 3],
            'INF value' => ['elements' => [1 => \INF], 'dimensions' => 3],
            'negative INF value' => ['elements' => [1 => -\INF], 'dimensions' => 3],
        ];
    }

    #[Test]
    public function can_normalize_integer_element_values_to_float(): void
    {
        $sparsevec = new Sparsevec([1 => 1, 3 => 2], 5);
        $this->assertSame([1 => 1.0, 3 => 2.0], $sparsevec->getElements());
    }

    #[Test]
    public function can_round_trip_from_string_to_string(): void
    {
        $original = '{1:1.5,3:2}/5';
        $sparsevec = Sparsevec::fromString($original);
        $this->assertSame($original, (string) $sparsevec);
    }
}
