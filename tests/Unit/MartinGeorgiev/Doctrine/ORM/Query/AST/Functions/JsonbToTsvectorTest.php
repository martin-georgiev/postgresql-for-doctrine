<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbToTsvector;

class JsonbToTsvectorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TO_TSVECTOR' => JsonbToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts jsonb to tsvector' => "SELECT jsonb_to_tsvector('english', c0_.jsonbObject1) AS sclr_0 FROM ContainsJsons c0_",
            'converts jsonb to tsvector with filter' => "SELECT jsonb_to_tsvector('english', c0_.jsonbObject1, '[\"string\", \"numeric\"]') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts jsonb to tsvector' => \sprintf("SELECT JSONB_TO_TSVECTOR('english', e.jsonbObject1) FROM %s e", ContainsJsons::class),
            'converts jsonb to tsvector with filter' => \sprintf("SELECT JSONB_TO_TSVECTOR('english', e.jsonbObject1, '[\"string\", \"numeric\"]') FROM %s e", ContainsJsons::class),
        ];
    }
}
