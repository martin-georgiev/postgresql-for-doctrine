<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength;

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
            'SELECT jsonb_array_length(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_ARRAY_LENGTH(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
