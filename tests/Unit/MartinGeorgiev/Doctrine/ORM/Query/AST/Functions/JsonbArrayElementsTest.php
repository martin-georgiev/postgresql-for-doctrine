<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements;

class JsonbArrayElementsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS' => JsonbArrayElements::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'expands jsonb array into separate rows' => 'SELECT jsonb_array_elements(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'expands jsonb array into separate rows' => \sprintf('SELECT JSONB_ARRAY_ELEMENTS(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
