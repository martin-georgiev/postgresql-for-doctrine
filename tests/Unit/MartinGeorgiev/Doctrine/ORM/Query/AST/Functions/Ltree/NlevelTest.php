<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\Nlevel;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class NlevelTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NLEVEL' => Nlevel::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'returns number of labels in path' => 'SELECT nlevel(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'returns number of labels in path' => \sprintf('SELECT NLEVEL(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
