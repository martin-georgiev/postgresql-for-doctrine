<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;

class TsmatchTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSMATCH' => Tsmatch::class,
        ];
    }

    public function test_tsmatch(): void
    {
        $dql = "SELECT TSMATCH('developer manager', 'developer') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
