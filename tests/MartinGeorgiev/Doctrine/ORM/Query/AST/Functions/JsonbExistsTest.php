<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

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
            "SELECT jsonb_exists(c0_.object, 'country') AS sclr_0 FROM ContainsJson c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf("SELECT JSONB_EXISTS(e.object, 'country') FROM %s e", ContainsJson::class),
        ];
    }
}
