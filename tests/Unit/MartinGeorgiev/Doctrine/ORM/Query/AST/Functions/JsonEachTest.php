<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach;

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
            'expands json object into key-value pairs' => 'SELECT json_each(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'expands json object into key-value pairs' => \sprintf('SELECT JSON_EACH(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
