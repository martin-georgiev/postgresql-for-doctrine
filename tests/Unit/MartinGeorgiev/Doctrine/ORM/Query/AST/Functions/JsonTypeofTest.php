<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof;

class JsonTypeofTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TYPEOF' => JsonTypeof::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'determines type of json document' => 'SELECT json_typeof(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
            'determines type of literal value' => "SELECT json_typeof('42') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'determines type of json document' => \sprintf('SELECT JSON_TYPEOF(e.object1) FROM %s e', ContainsJsons::class),
            'determines type of literal value' => \sprintf("SELECT JSON_TYPEOF('42') FROM %s e", ContainsJsons::class),
        ];
    }
}
