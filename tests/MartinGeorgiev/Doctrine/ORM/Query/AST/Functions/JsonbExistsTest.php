<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists;

class JsonbExistsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EXISTS' => JsonbExists::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT jsonb_exists(c0_.object1, 'country') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSONB_EXISTS(e.object1, 'country') FROM %s e", ContainsJsons::class),
        ];
    }
}
