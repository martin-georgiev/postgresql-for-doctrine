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
        $this->assertTrue($result[0]['result']);
    }

    public function test_tsmatch_negative(): void
    {
        $dql = "SELECT TSMATCH('developer manager', 'doctor') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
