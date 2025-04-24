<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty;

class JsonbPrettyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PRETTY' => JsonbPretty::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'formats jsonb document with proper indentation' => 'SELECT jsonb_pretty(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
            'formats literal jsonb value' => "SELECT jsonb_pretty('{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'formats jsonb document with proper indentation' => \sprintf('SELECT JSONB_PRETTY(e.object1) FROM %s e', ContainsJsons::class),
            'formats literal jsonb value' => \sprintf("SELECT JSONB_PRETTY('{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
        ];
    }
}
