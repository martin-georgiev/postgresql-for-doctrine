<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonEachTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EACH' => JsonEach::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_each(c0_.object) AS sclr_0 FROM ContainsJson c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_EACH(e.object) FROM %s e', ContainsJson::class),
        ];
    }
}
