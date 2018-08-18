<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbArrayElementsTextTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSONB_ARRAY_ELEMENTS_TEXT' => JsonbArrayElementsText::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT jsonb_array_elements_text(c0_.object) AS sclr_0 FROM ContainsJson c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT JSONB_ARRAY_ELEMENTS_TEXT(e.object) FROM %s e', ContainsJson::class);
    }
}
