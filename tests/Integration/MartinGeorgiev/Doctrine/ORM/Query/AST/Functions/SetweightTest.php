<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Setweight;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use PHPUnit\Framework\Attributes\Test;

class SetweightTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'SETWEIGHT' => Setweight::class,
            'STRIP' => Strip::class,
        ];
    }

    #[Test]
    public function can_assign_weight_to_tsvector(): void
    {
        $dql = "SELECT SETWEIGHT(TO_TSVECTOR(t.text1), 'A') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3A 'ipsum':2A 'lorem':1A", $result[0]['result']);
    }

    #[Test]
    public function can_assign_weight_to_literal_tsvector(): void
    {
        $dql = "SELECT SETWEIGHT(TO_TSVECTOR('lorem ipsum dolor'), 'B') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor':3B 'ipsum':2B 'lorem':1B", $result[0]['result']);
    }

    #[Test]
    public function has_no_effect_on_stripped_tsvector(): void
    {
        $dql = "SELECT SETWEIGHT(STRIP(TO_TSVECTOR(t.text1)), 'A') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }
}
