<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ErrorOnNull;

final class ErrorOnNullTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERROR_ON_NULL' => ErrorOnNull::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks an entity field' => 'SELECT error_on_null(c0_.integer1) AS sclr_0 FROM ContainsNumerics c0_',
            'checks a numeric literal' => 'SELECT error_on_null(42) AS sclr_0 FROM ContainsNumerics c0_',
            'checks an arithmetic expression' => 'SELECT error_on_null(c0_.integer1 + c0_.integer2) AS sclr_0 FROM ContainsNumerics c0_',
            'checks a parameter' => 'SELECT error_on_null(?) AS sclr_0 FROM ContainsNumerics c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks an entity field' => \sprintf('SELECT ERROR_ON_NULL(e.integer1) FROM %s e', ContainsNumerics::class),
            'checks a numeric literal' => \sprintf('SELECT ERROR_ON_NULL(42) FROM %s e', ContainsNumerics::class),
            'checks an arithmetic expression' => \sprintf('SELECT ERROR_ON_NULL(e.integer1 + e.integer2) FROM %s e', ContainsNumerics::class),
            'checks a parameter' => \sprintf('SELECT ERROR_ON_NULL(:value) FROM %s e', ContainsNumerics::class),
        ];
    }
}
