<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha256;

class Sha256Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA256' => Sha256::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sha256 of a string' => "SELECT sha256('Hello Doctrine'::bytea) AS sclr_0 FROM ContainsTexts c0_",
            'computes sha256 of text field' => 'SELECT sha256(c0_.text1::bytea) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sha256 of a string' => \sprintf("SELECT SHA256('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes sha256 of text field' => \sprintf('SELECT SHA256(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
