<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class TsmatchTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TO_TSVECTOR' => ToTsvector::class,
            'TSMATCH' => Tsmatch::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (to_tsvector(c0_.text1) @@ to_tsquery('testing')) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT TSMATCH(TO_TSVECTOR(e.text1), TO_TSQUERY('testing')) FROM %s e", ContainsTexts::class),
        ];
    }
}
