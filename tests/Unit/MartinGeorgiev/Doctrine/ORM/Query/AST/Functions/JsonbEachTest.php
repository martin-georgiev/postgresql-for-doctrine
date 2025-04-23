<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach;

class JsonbEachTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EACH' => JsonbEach::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'expands top-level jsonb object into key-value pairs' => 'SELECT jsonb_each(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'expands top-level jsonb object into key-value pairs' => \sprintf('SELECT JSONB_EACH(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
