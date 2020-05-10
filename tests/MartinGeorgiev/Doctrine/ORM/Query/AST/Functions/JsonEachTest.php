<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsJsons;

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
            'SELECT json_each(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_EACH(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
