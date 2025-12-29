<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha224;

class Sha224Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA224' => Sha224::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sha224 of a string' => "SELECT sha224('Hello Doctrine'::bytea) AS sclr_0 FROM ContainsTexts c0_",
            'computes sha224 of text field' => 'SELECT sha224(c0_.text1::bytea) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sha224 of a string' => \sprintf("SELECT SHA224('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes sha224 of text field' => \sprintf('SELECT SHA224(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
