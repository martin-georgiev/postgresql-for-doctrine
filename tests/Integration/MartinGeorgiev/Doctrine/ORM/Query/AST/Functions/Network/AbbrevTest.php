<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Network\Abbrev;
use PHPUnit\Framework\Attributes\Test;

class AbbrevTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ABBREV' => Abbrev::class,
        ];
    }

    #[Test]
    public function returns_abbreviated_form_from_literal(): void
    {
        $dql = "SELECT ABBREV('192.168.1.5/24') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5/24', $result[0]['result']);
    }

    #[Test]
    public function returns_abbreviated_form_from_field(): void
    {
        $dql = 'SELECT ABBREV(t.ip) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNetworks t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals('192.168.1.5/24', $result[0]['result']);
    }
}
