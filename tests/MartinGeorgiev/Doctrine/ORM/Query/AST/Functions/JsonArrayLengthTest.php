<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonArrayLength;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsJsons;

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
            'SELECT json_array_length(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_ARRAY_LENGTH(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
