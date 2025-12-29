<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha512;

class Sha512Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA512' => Sha512::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sha512 of a string' => "SELECT sha512('Hello Doctrine'::bytea) AS sclr_0 FROM ContainsTexts c0_",
            'computes sha512 of text field' => 'SELECT sha512(c0_.text1::bytea) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sha512 of a string' => \sprintf("SELECT SHA512('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes sha512 of text field' => \sprintf('SELECT SHA512(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
