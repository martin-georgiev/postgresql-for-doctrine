<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Traits;

use MartinGeorgiev\Utils\PostgresBackslashEscaper;

/**
 * Provides HSTORE string parsing and building shared by Hstore and HstoreArray types.
 *
 * @since 4.6
 */
trait HstoreParserTrait
{
    /**
     * Matches a quoted key, '=>', then either a quoted value or the NULL keyword.
     *
     * @var string
     */
    private const HSTORE_PAIR_PATTERN = '/"((?:[^"\\\\]|\\\\.)*)"\\s*=>\\s*(?:"((?:[^"\\\\]|\\\\.)*)"|(?i:(NULL)))/';

    /**
     * Spelled out rather than left to `\s`, which leaves out the form feed and vertical tab PostgreSQL skips.
     *
     * @var string
     */
    private const HSTORE_WHITESPACE = " \t\n\r\f\v";

    abstract protected function throwInvalidHstoreValueTypeException(mixed $value): never;

    abstract protected function throwInvalidHstoreFormatException(string $value): never;

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

    private function assertSeparatesPairs(string $skipped, bool $followsAPair, string $value): void
    {
        $expected = $followsAPair ? ',' : '';
        if (\trim($skipped, self::HSTORE_WHITESPACE) !== $expected) {
            $this->throwInvalidHstoreFormatException($value);
        }
    }

    /**
     * PostgreSQL reads a comma after the last pair.
     */
    private function assertClosesTheLiteral(string $tail, bool $followsAPair, string $value): void
    {
        $accepted = $followsAPair ? ['', ','] : [''];
        if (!\in_array(\trim($tail, self::HSTORE_WHITESPACE), $accepted, true)) {
            $this->throwInvalidHstoreFormatException($value);
        }
    }

    /**
     * @param array<array-key, mixed> $pairs
     */
    private function buildHstoreString(array $pairs): string
    {
        $result = [];
        foreach ($pairs as $key => $value) {
            $escapedKey = PostgresBackslashEscaper::escape((string) $key);
            if ($value === null) {
                $result[] = \sprintf('"%s"=>NULL', $escapedKey);
            } elseif (\is_string($value)) {
                $escapedValue = PostgresBackslashEscaper::escape($value);
                $result[] = \sprintf('"%s"=>"%s"', $escapedKey, $escapedValue);
            } else {
                $this->throwInvalidHstoreValueTypeException($value);
            }
        }

        return \implode(',', $result);
    }
}
