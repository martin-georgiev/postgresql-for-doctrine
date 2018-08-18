<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonStripNullsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSON_STRIP_NULLS' => JsonStripNulls::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT json_strip_nulls(c0_.object) AS sclr_0 FROM ContainsJson c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT JSON_STRIP_NULLS(e.object) FROM %s e', ContainsJson::class);
    }
}
