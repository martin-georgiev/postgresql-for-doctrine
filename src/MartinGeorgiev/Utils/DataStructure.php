<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

/**
 * Util class with helpers for working with PostgreSql data structures.
 *
 * @since 0.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DataStructure
{
    /**
     * This method supports only single-dimensioned text arrays and
     * relays on the default escaping strategy in PostgreSql (double quotes).
     */
    public static function transformPostgresTextArrayToPHPArray(string $postgresArray): array
    {
        $transform = static function (string $textArrayToTransform): array {
            $indicatesMultipleDimensions = \mb_strpos($textArrayToTransform, '},{') !== false
                || \mb_strpos($textArrayToTransform, '{{') === 0;
            if ($indicatesMultipleDimensions) {
                throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
            }

            $phpArray = \str_getcsv(\trim($textArrayToTransform, '{}'));
            foreach ($phpArray as $i => $text) {
                if ($text === null) {
                    unset($phpArray[$i]);

                    break;
                }

                $isInteger = \is_numeric($text) && ''.(int) $text === $text;
                if ($isInteger) {
                    $phpArray[$i] = (int) $text;

                    continue;
                }

                $isFloat = \is_numeric($text) && ''.(float) $text === $text;
                if ($isFloat) {
                    $phpArray[$i] = (float) $text;

                    continue;
                }

                if (!\is_string($text)) {
                    $exceptionMessage = 'Unsupported data type encountered. Expected null, integer, float or string value. Instead it is "%s".';

                    throw new \InvalidArgumentException(\sprintf($exceptionMessage, \gettype($text)));
                }

                $phpArray[$i] = \stripslashes(\str_replace('\"', '"', $text));
            }

            return $phpArray;
        };

        return $transform($postgresArray);
    }

    /**
     * This method supports only single-dimensioned PHP arrays.
     * This method relays on the default escaping strategy in PostgreSql (double quotes).
     *
     * @see https://stackoverflow.com/a/5632171/3425372 Kudos to jmz for the inspiration
     */
    public static function transformPHPArrayToPostgresTextArray(array $phpArray): string
    {
        $transform = static function (array $phpArrayToTransform): string {
            $result = [];
            foreach ($phpArrayToTransform as $text) {
                if (\is_array($text)) {
                    throw new \InvalidArgumentException('Only single-dimensioned arrays are supported');
                }

                $escapedText = \is_numeric($text) || \ctype_digit($text) ? $text : \sprintf('"%s"', \addcslashes($text, '"\\'));
                $result[] = $escapedText;
            }

            return '{'.\implode(',', $result).'}';
        };

        return $transform($phpArray);
    }
}
