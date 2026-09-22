<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Reverse;
use PHPUnit\Framework\Attributes\Test;

final class ReverseTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REVERSE' => Reverse::class,
        ];
    }

    #[Test]
    public function returns_the_reversed_value_from_an_entity_field(): void
    {
        $dql = 'SELECT REVERSE(t.text1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('oof', $result[0]['result']);
    }

    #[Test]
    public function returns_the_reversed_value_from_a_literal(): void
    {
        $dql = "SELECT REVERSE('foo') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('oof', $result[0]['result']);
    }
}
