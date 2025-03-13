<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize;

class JsonSerializeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_SERIALIZE' => JsonSerialize::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            // Basic serialization
            'SELECT json_serialize(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
            // With expression
            'SELECT json_serialize(UPPER(c0_.object1)) AS sclr_0 FROM ContainsJsons c0_',
            // With literal
            "SELECT json_serialize('{\"key\": \"value\"}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_SERIALIZE(e.object1) FROM %s e', ContainsJsons::class),
            \sprintf('SELECT JSON_SERIALIZE(UPPER(e.object1)) FROM %s e', ContainsJsons::class),
            \sprintf("SELECT JSON_SERIALIZE('{\"key\": \"value\"}') FROM %s e", ContainsJsons::class),
        ];
    }
}
