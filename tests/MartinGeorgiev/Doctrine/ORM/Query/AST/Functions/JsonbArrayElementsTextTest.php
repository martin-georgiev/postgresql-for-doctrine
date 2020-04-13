<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJsons;

class JsonbArrayElementsTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS_TEXT' => JsonbArrayElementsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_array_elements_text(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_ARRAY_ELEMENTS_TEXT(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
