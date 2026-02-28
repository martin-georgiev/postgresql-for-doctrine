<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use PHPUnit\Framework\Attributes\Test;

class StripTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'STRIP' => Strip::class,
        ];
    }

    #[Test]
    public function can_strip_positions_from_tsvector(): void
    {
        $dql = 'SELECT STRIP(TO_TSVECTOR(t.text1)) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }

    #[Test]
    public function stripped_tsvector_differs_from_original(): void
    {
        $dql = 'SELECT TO_TSVECTOR(t.text1) as original, STRIP(TO_TSVECTOR(t.text1)) as stripped FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);

        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['stripped']);
        $this->assertNotSame($result[0]['original'], $result[0]['stripped']);
    }

    #[Test]
    public function can_strip_positions_from_literal_tsvector(): void
    {
        $dql = "SELECT STRIP(TO_TSVECTOR('lorem ipsum dolor')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }
}
