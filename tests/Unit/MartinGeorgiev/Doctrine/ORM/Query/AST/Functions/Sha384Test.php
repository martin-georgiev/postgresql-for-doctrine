<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha384;

class Sha384Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA384' => Sha384::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'computes sha384 of a string' => "SELECT sha384('Hello Doctrine'::bytea) AS sclr_0 FROM ContainsTexts c0_",
            'computes sha384 of text field' => 'SELECT sha384(c0_.text1::bytea) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'computes sha384 of a string' => \sprintf("SELECT SHA384('Hello Doctrine') FROM %s e", ContainsTexts::class),
            'computes sha384 of text field' => \sprintf('SELECT SHA384(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
