<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReverseBytes;

class ReverseBytesTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REVERSE_BYTES' => ReverseBytes::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'reverses bytes' => "SELECT reverse('test'::bytea) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'reverses bytes' => \sprintf("SELECT REVERSE_BYTES('test') FROM %s e", ContainsArrays::class),
        ];
    }
}
