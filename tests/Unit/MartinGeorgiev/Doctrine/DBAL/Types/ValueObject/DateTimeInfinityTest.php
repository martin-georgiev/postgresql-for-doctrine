<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject;

use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\DateTimeInfinity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateTimeInfinityTest extends TestCase
{
    #[DataProvider('provideAcceptedSpellings')]
    #[Test]
    public function parses_every_accepted_spelling(string $token, DateTimeInfinity $dateTimeInfinity): void
    {
        $this->assertSame($dateTimeInfinity, DateTimeInfinity::tryFromString($token));
    }

    /**
     * @return array<string, array{string, DateTimeInfinity}>
     */
    public static function provideAcceptedSpellings(): array
    {
        return [
            'canonical positive' => ['infinity', DateTimeInfinity::POSITIVE],
            'canonical negative' => ['-infinity', DateTimeInfinity::NEGATIVE],
            'capitalized' => ['Infinity', DateTimeInfinity::POSITIVE],
            'upper case' => ['INFINITY', DateTimeInfinity::POSITIVE],
            'explicit plus' => ['+infinity', DateTimeInfinity::POSITIVE],
        ];
    }

    #[DataProvider('provideRejectedSpellings')]
    #[Test]
    public function rejects_spelling_a_date_does_not_accept(string $token): void
    {
        $this->assertNull(DateTimeInfinity::tryFromString($token));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideRejectedSpellings(): array
    {
        return [
            'float abbreviation' => ['inf'],
            'negative float abbreviation' => ['-inf'],
            'not a number' => ['NaN'],
            'doubled plus' => ['++infinity'],
            'plus before minus' => ['+-infinity'],
            'minus before plus' => ['-+infinity'],
            'a lone sign' => ['+'],
            'a date' => ['2024-01-01'],
            'empty' => [''],
        ];
    }
}
