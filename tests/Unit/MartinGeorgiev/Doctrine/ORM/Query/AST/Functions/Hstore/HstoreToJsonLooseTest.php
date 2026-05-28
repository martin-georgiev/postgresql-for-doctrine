<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJsonLoose;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class HstoreToJsonLooseTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_TO_JSON_LOOSE' => HstoreToJsonLoose::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts hstore field to json with loose typing' => 'SELECT hstore_to_json_loose(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts hstore field to json with loose typing' => \sprintf('SELECT HSTORE_TO_JSON_LOOSE(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
