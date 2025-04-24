<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText;

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
            'expands json object into text key-value pairs' => 'SELECT json_each_text(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'expands json object into text key-value pairs' => \sprintf('SELECT JSON_EACH_TEXT(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
