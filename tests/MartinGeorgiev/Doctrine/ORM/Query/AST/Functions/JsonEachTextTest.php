<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonEachTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EACH_TEXT' => JsonEachText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_each_text(c0_.object) AS sclr_0 FROM ContainsJson c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT JSON_EACH_TEXT(e.object) FROM %s e', ContainsJson::class),
        ];
    }
}
