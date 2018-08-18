<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayLength;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonbArrayLengthTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSONB_ARRAY_LENGTH' => JsonbArrayLength::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT jsonb_array_length(c0_.object) AS sclr_0 FROM ContainsJson c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT JSONB_ARRAY_LENGTH(e.object) FROM %s e', ContainsJson::class);
    }
}
