<?php

namespace MartinGeorgiev\Utils;

/**
 * Util class with helpers for working with PostgreSql data structures
 *
 * @since 0.9
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class DataStructure
{
    /**
     * This method provides support only for the default escaping strategy in PostgreSql (double quotes).
     *
     * @see https://stackoverflow.com/a/27964420/3425372 Kudos to dmikam for the solution
     * @param string $postgresArray
     * @return array
     */
    public static function transformPostgresTextArrayToPHPArray($postgresArray)
    {
        $parse = function($stringToParse, $startPosition = 0) use (&$parse)
        {
            if (empty($stringToParse) || $stringToParse[0] != '{') {
                return null;
            }
            $result = [];
            $isString = false;
            $quote = '';
            $length = strlen($stringToParse);
            $value = '';
            for ($i = $startPosition + 1; $i < $length; $i++) {
                $currentCharacter = $stringToParse[$i];

                if (!$isString && $currentCharacter == '}') {
                    if ($value !== '' || !empty($result)) {
                        $result[] = $value;
                    }
                    break;
                    
                } elseif (!$isString && $currentCharacter == '{') {
                    $value = $parse($stringToParse, $i);

                } elseif (!$isString && $currentCharacter == ','){
                    $result[] = $value;
                    $value = '';

                } elseif (!$isString && ($currentCharacter == '"' || $currentCharacter == "'")) {
                    $isString = true;
                    $quote = $currentCharacter;

                } elseif ($isString && $currentCharacter == $quote && $stringToParse[$i - 1] == "\\") {
                    $value = substr($value, 0, -1) . $currentCharacter;

                } elseif ($isString && $currentCharacter == $quote && $stringToParse[$i - 1] != "\\") {
                    $isString = false;

                } else {
                    $value .= $currentCharacter;
                }
            }

            return $result;
        };

        return $parse($postgresArray);
    }

    /**
     * This method provides support only for the default escaping strategy in PostgreSql (double quotes).
     * 
     * @see https://stackoverflow.com/a/5632171/3425372 Kudos to jmz for the solution
     * @param array $phpArray
     * @return string
     */
    public static function transformPHPArrayToPostgresTextArray(array $phpArray)
    {
        $transform = function ($arrayToTransform) use (&$transform) {
            settype($arrayToTransform, 'array');
            $result = [];
            foreach ($arrayToTransform as $text) {
                if (is_array($text)) {
                    $result[] = $transform($text);
                    continue;
                }
                $text = str_replace('"', '\\"', $text);
                $result[] = ctype_digit($text) ? $text : '"' . $text . '"';
            }
            return '{' . implode(",", $result) . '}';
        };
        
        return $transform($phpArray);
    }
}
