<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreToJson;

class HstoreToJsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_TO_JSON' => HstoreToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts hstore field to json' => 'SELECT hstore_to_json(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts hstore field to json' => \sprintf('SELECT HSTORE_TO_JSON(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
