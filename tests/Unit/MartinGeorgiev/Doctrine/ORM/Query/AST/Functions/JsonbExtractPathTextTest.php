<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExtractPathText;

class JsonbExtractPathTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EXTRACT_PATH_TEXT' => JsonbExtractPathText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts path text from jsonb' => "SELECT jsonb_extract_path_text(c0_.jsonbObject1, 'key1', 'key2') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts path text from jsonb' => \sprintf("SELECT JSONB_EXTRACT_PATH_TEXT(e.jsonbObject1, 'key1', 'key2') FROM %s e", ContainsJsons::class),
        ];
    }
}
