<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart;
use PHPUnit\Framework\Attributes\Test;

final class SplitPartTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SPLIT_PART' => SplitPart::class,
        ];
    }

    #[Test]
    public function returns_the_requested_part_from_an_entity_field(): void
    {
        $dql = "SELECT SPLIT_PART(t.text1, ',', 1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('special', $result[0]['result']);
    }

    #[Test]
    public function returns_the_requested_part_from_a_literal(): void
    {
        $dql = "SELECT SPLIT_PART('special,chars;test', ',', 2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('chars;test', $result[0]['result']);
    }
}
