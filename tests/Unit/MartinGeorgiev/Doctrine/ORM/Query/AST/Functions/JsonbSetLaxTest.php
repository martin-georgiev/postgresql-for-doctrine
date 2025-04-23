<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax;

class JsonbSetLaxTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET_LAX' => JsonbSetLax::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'modifies top-level property' => "SELECT jsonb_set_lax(c0_.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
            'sets property to null' => "SELECT jsonb_set_lax(c0_.object1, '{country}', null) AS sclr_0 FROM ContainsJsons c0_",
            'modifies nested property' => "SELECT jsonb_set_lax(c0_.object1, '{address,city}', '\"Sofia\"') AS sclr_0 FROM ContainsJsons c0_",
            'modifies array element at index' => "SELECT jsonb_set_lax(c0_.object1, '{phones,0}', '\"+1234567890\"') AS sclr_0 FROM ContainsJsons c0_",
            'uses parameters for path and value' => 'SELECT jsonb_set_lax(c0_.object1, ?, ?) AS sclr_0 FROM ContainsJsons c0_',
            'modifies deeply nested array element' => "SELECT jsonb_set_lax(c0_.object1, '{user,contacts,0,phone}', '\"+1234567890\"') AS sclr_0 FROM ContainsJsons c0_",
            'sets boolean property' => "SELECT jsonb_set_lax(c0_.object1, '{is_active}', 'true') AS sclr_0 FROM ContainsJsons c0_",
            'sets numeric property' => "SELECT jsonb_set_lax(c0_.object1, '{count}', '42') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'modifies top-level property' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
            'sets property to null' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{country}', null) FROM %s e", ContainsJsons::class),
            'modifies nested property' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{address,city}', '\"Sofia\"') FROM %s e", ContainsJsons::class),
            'modifies array element at index' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{phones,0}', '\"+1234567890\"') FROM %s e", ContainsJsons::class),
            'uses parameters for path and value' => \sprintf('SELECT JSONB_SET_LAX(e.object1, :path, :value) FROM %s e', ContainsJsons::class),
            'modifies deeply nested array element' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{user,contacts,0,phone}', '\"+1234567890\"') FROM %s e", ContainsJsons::class),
            'sets boolean property' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{is_active}', 'true') FROM %s e", ContainsJsons::class),
            'sets numeric property' => \sprintf("SELECT JSONB_SET_LAX(e.object1, '{count}', '42') FROM %s e", ContainsJsons::class),
        ];
    }
}
