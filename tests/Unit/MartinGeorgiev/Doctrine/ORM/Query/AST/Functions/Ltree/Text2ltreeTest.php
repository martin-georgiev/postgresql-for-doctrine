<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Text2ltree;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class Text2ltreeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEXT2LTREE' => Text2ltree::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'casts text to ltree' => "SELECT text2ltree('Top.Child1.Child2') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'casts text to ltree' => \sprintf("SELECT TEXT2LTREE('Top.Child1.Child2') FROM %s e", ContainsTexts::class),
        ];
    }
}
