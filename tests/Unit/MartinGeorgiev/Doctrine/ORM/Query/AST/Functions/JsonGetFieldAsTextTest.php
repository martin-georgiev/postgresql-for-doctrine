<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText;

class JsonGetFieldAsTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD_AS_TEXT' => JsonGetFieldAsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object1 ->> 'country') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_GET_FIELD_AS_TEXT(e.object1, 'country') FROM %s e", ContainsJsons::class),
        ];
    }
}
