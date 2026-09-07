<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenRandomUuid;

final class GenRandomUuidTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GEN_RANDOM_UUID' => GenRandomUuid::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates random uuid' => 'SELECT gen_random_uuid() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates random uuid' => \sprintf('SELECT GEN_RANDOM_UUID() FROM %s e', ContainsTexts::class),
        ];
    }
}
