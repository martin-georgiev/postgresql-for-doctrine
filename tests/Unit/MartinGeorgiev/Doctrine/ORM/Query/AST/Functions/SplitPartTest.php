<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart;

class SplitPartTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPLIT_PART' => SplitPart::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT split_part(c0_.text1, ',', 1) AS sclr_0 FROM ContainsTexts c0_",
            "SELECT split_part(c0_.text2, '-', -2) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT SPLIT_PART(e.text1, ',', 1) FROM %s e", ContainsTexts::class),
            \sprintf("SELECT SPLIT_PART(e.text2, '-', -2) FROM %s e", ContainsTexts::class),
        ];
    }
}
