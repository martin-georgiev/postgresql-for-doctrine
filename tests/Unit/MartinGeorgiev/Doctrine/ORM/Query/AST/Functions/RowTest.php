<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsIntegers;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row;

class RowTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Row('ROW');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ROW' => Row::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ROW(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT ROW(c0_.date1, c0_.date2) AS sclr_0 FROM ContainsDates c0_',
            'SELECT ROW(c0_.object1, c0_.object2) AS sclr_0 FROM ContainsJsons c0_',
            "SELECT c0_.id AS id_0 FROM ContainsTexts c0_ WHERE ROW(c0_.text1, c0_.text2) > ROW('test', 'test')",
            "SELECT c0_.id AS id_0 FROM ContainsIntegers c0_ WHERE ROW(c0_.integer1, c0_.integer2, 'This is a test') > ROW(1, 2, 'This')",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ROW(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT ROW(e.date1, e.date2) FROM %s e', ContainsDates::class),
            \sprintf('SELECT ROW(e.object1, e.object2) FROM %s e', ContainsJsons::class),
            \sprintf("SELECT e.id FROM %s e WHERE ROW(e.text1, e.text2) > ROW('test', 'test')", ContainsTexts::class),
            \sprintf("SELECT e.id FROM %s e WHERE ROW(e.integer1, e.integer2, 'This is a test') > ROW(1, 2, 'This')", ContainsIntegers::class),
        ];
    }
}
