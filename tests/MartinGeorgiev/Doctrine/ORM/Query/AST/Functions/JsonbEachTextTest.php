<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsJsons;

class JsonbEachTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EACH_TEXT' => JsonbEachText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_each_text(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_EACH_TEXT(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
