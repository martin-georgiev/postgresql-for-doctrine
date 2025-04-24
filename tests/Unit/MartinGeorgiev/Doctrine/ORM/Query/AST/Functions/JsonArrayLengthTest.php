<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength;

class JsonArrayLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_ARRAY_LENGTH' => JsonArrayLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets length of top-level array' => 'SELECT json_array_length(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
            'gets length from literal json' => "SELECT json_array_length('{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets length of top-level array' => \sprintf('SELECT JSON_ARRAY_LENGTH(e.object1) FROM %s e', ContainsJsons::class),
            'gets length from literal json' => \sprintf("SELECT JSON_ARRAY_LENGTH('{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
        ];
    }
}
