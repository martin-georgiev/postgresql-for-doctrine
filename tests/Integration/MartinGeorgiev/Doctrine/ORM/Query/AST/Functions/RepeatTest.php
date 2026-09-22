<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Repeat;
use PHPUnit\Framework\Attributes\Test;

final class RepeatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REPEAT' => Repeat::class,
        ];
    }

    #[Test]
    public function returns_the_repeated_value_from_an_entity_field(): void
    {
        $dql = 'SELECT REPEAT(t.text1, 2) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t 
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foofoo', $result[0]['result']);
    }

    #[Test]
    public function returns_the_repeated_value_from_a_literal(): void
    {
        $dql = "SELECT REPEAT('foo', 2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foofoo', $result[0]['result']);
    }
}
