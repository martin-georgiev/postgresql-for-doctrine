<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar;

class ToCharTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_char' => ToChar::class,
        ];
    }

    public function test_tochar(): void
    {
        $dql = "SELECT to_char(t.datetimetz1, 'HH12:MI:SS') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('10:30:00', $result[0]['result']);
    }
}
