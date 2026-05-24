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

    abstract protected function createInvalidHstoreValueTypeException(mixed $value): ConversionException;

    /**
     * @return array<string, string|null>
     */
    private function parseHstoreString(string $value): array
    {
        $result = [];
        \preg_match_all(self::HSTORE_PAIR_PATTERN, $value, $matches, \PREG_SET_ORDER);

        foreach ($matches as $match) {
            $key = \str_replace(['\\\\', '\\"'], ['\\', '"'], $match[1]);
            $result[$key] = isset($match[3]) ? null : \str_replace(['\\\\', '\\"'], ['\\', '"'], $match[2] ?? '');
        }

        return $result;
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
