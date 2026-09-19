<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use Doctrine\DBAL\Types\ConversionException;

/**
 * Provides HSTORE string parsing and building shared by Hstore and HstoreArray types.
 *
 * @since 4.6
 */
trait HstoreParserTrait
{
    /**
     * Matches a quoted key, '=>', then either a quoted value or the NULL keyword.
     */
    private const HSTORE_PAIR_PATTERN = '/"((?:[^"\\\\]|\\\\.)*)"\\s*=>\\s*(?:"((?:[^"\\\\]|\\\\.)*)"|(?i:(NULL)))/';

    /**
     * The characters PostgreSQL skips between hstore tokens. Form feed and vertical tab count, which is why this is
     * spelled out rather than left to `\s`.
     *
     * @var string
     */
    private const HSTORE_WHITESPACE = " \t\n\r\f\v";

    abstract protected function createInvalidHstoreValueTypeException(mixed $value): ConversionException;

    abstract protected function createInvalidHstoreFormatException(string $value): ConversionException;

    /**
     * @return array<string, string|null>
     */
    private function parseHstoreString(string $value): array
    {
        $result = [];
        \preg_match_all(self::HSTORE_PAIR_PATTERN, $value, $matches, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE);

        $consumedUpTo = 0;
        foreach ($matches as $index => $match) {
            [$pair, $offset] = $match[0];
            $this->assertSeparatesPairs(\substr($value, $consumedUpTo, $offset - $consumedUpTo), $index > 0, $value);
            $consumedUpTo = $offset + \strlen($pair);

            $key = \str_replace(['\\\\', '\\"'], ['\\', '"'], $match[1][0]);
            $result[$key] = isset($match[3]) ? null : \str_replace(['\\\\', '\\"'], ['\\', '"'], $match[2][0] ?? '');
        }

        $this->assertClosesTheLiteral(\substr($value, $consumedUpTo), $matches !== [], $value);

        return $result;
    }

    /**
     * Whatever the pair pattern skipped over has to be what PostgreSQL writes there and nothing else: one comma
     * between two pairs, and only whitespace before the first. Text it would not have written is a literal that
     * reading as a map of the fragments that matched would lose.
     */
    private function assertSeparatesPairs(string $skipped, bool $followsAPair, string $value): void
    {
        $expected = $followsAPair ? ',' : '';
        if (\trim($skipped, self::HSTORE_WHITESPACE) !== $expected) {
            throw $this->createInvalidHstoreFormatException($value);
        }
    }

    /**
     * PostgreSQL reads a comma after the last pair, so the tail carries at most that one - and a comma with no pair
     * in front of it is a literal it turns away.
     */
    private function assertClosesTheLiteral(string $tail, bool $followsAPair, string $value): void
    {
        $accepted = $followsAPair ? ['', ','] : [''];
        if (!\in_array(\trim($tail, self::HSTORE_WHITESPACE), $accepted, true)) {
            throw $this->createInvalidHstoreFormatException($value);
        }
    }

    /**
     * @param array<array-key, mixed> $pairs
     */
    private function buildHstoreString(array $pairs): string
    {
        $result = [];
        foreach ($pairs as $key => $value) {
            $escapedKey = \str_replace(['\\', '"'], ['\\\\', '\\"'], (string) $key);
            if ($value === null) {
                $result[] = \sprintf('"%s"=>NULL', $escapedKey);
            } elseif (\is_string($value)) {
                $escapedValue = \str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
                $result[] = \sprintf('"%s"=>"%s"', $escapedKey, $escapedValue);
            } else {
                throw $this->createInvalidHstoreValueTypeException($value);
            }
        }

        return \implode(',', $result);
    }
}
