<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbEachTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSONB_EACH' => JsonbEach::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT jsonb_each(c0_.object) AS sclr_0 FROM ContainsJson c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT JSONB_EACH(e.object) FROM %s e', ContainsJson::class);
    }
}
