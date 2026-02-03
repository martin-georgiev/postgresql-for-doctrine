<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExtractPath;

class JsonExtractPathTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EXTRACT_PATH' => JsonExtractPath::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts path from json' => "SELECT json_extract_path(c0_.jsonObject1, 'key1', 'key2') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts path from json' => \sprintf("SELECT JSON_EXTRACT_PATH(e.jsonObject1, 'key1', 'key2') FROM %s e", ContainsJsons::class),
        ];
    }
}
