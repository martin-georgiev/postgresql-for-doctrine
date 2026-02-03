<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPath;

class JsonbExtractPathTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EXTRACT_PATH' => JsonbExtractPath::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts path from jsonb' => "SELECT jsonb_extract_path(c0_.jsonbObject1, 'key1', 'key2') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts path from jsonb' => \sprintf("SELECT JSONB_EXTRACT_PATH(e.jsonbObject1, 'key1', 'key2') FROM %s e", ContainsJsons::class),
        ];
    }
}
