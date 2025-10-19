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
            'splits text and returns first part' => "SELECT split_part(c0_.text1, ',', 1) AS sclr_0 FROM ContainsTexts c0_",
            'splits text and returns part from end' => "SELECT split_part(c0_.text2, '-', -2) AS sclr_0 FROM ContainsTexts c0_",
            'splits with zero field number' => "SELECT split_part(c0_.text1, ',', 0) AS sclr_0 FROM ContainsTexts c0_",
            'splits with large field number' => "SELECT split_part(c0_.text1, ',', 999) AS sclr_0 FROM ContainsTexts c0_",
            'splits with single character delimiter' => "SELECT split_part(c0_.text1, '|', 2) AS sclr_0 FROM ContainsTexts c0_",
            'splits with multi-character delimiter' => "SELECT split_part(c0_.text1, ':::', 1) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'splits text and returns first part' => \sprintf("SELECT SPLIT_PART(e.text1, ',', 1) FROM %s e", ContainsTexts::class),
            'splits text and returns part from end' => \sprintf("SELECT SPLIT_PART(e.text2, '-', -2) FROM %s e", ContainsTexts::class),
            'splits with zero field number' => \sprintf("SELECT SPLIT_PART(e.text1, ',', 0) FROM %s e", ContainsTexts::class),
            'splits with large field number' => \sprintf("SELECT SPLIT_PART(e.text1, ',', 999) FROM %s e", ContainsTexts::class),
            'splits with single character delimiter' => \sprintf("SELECT SPLIT_PART(e.text1, '|', 2) FROM %s e", ContainsTexts::class),
            'splits with multi-character delimiter' => \sprintf("SELECT SPLIT_PART(e.text1, ':::', 1) FROM %s e", ContainsTexts::class),
        ];
    }
}
