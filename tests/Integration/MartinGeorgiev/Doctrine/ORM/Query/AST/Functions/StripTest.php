<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use PHPUnit\Framework\Attributes\Test;

final class StripTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'STRIP' => Strip::class,
        ];
    }

    #[Test]
    public function returns_a_stripped_tsvector_from_an_entity_field(): void
    {
        $dql = 'SELECT STRIP(TO_TSVECTOR(t.text1)) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }

    #[Test]
    public function returns_a_stripped_tsvector_from_a_literal(): void
    {
        $dql = "SELECT STRIP(TO_TSVECTOR('lorem ipsum dolor')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }
}
