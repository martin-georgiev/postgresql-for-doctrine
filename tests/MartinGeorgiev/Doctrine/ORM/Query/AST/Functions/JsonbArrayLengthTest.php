<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbArrayLengthTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_LENGTH' => JsonbArrayLength::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_array_length(c0_.object) AS sclr_0 FROM ContainsJson c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_ARRAY_LENGTH(e.object) FROM %s e', ContainsJson::class),
        ];
    }
}
