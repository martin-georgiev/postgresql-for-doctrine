<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount;
use PHPUnit\Framework\Attributes\Test;

final class RegexpCountTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(150000, 'REGEXP_COUNT function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_COUNT' => RegexpCount::class,
        ];
    }

    #[Test]
    public function returns_the_match_count_from_an_entity_field(): void
    {
        // Row 1 text1 is 'this is a test string' - contains 4 occurrences of 't': This, TesT, sTring
        $dql = "SELECT REGEXP_COUNT(t.text1, 't') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(4, $result[0]['result']);
    }

    #[Test]
    public function returns_the_match_count_with_a_start_position(): void
    {
        $dql = "SELECT REGEXP_COUNT('this is a test string', 't', 5) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
