<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount;
use PHPUnit\Framework\Attributes\Test;

class RegexpCountTest extends TextTestCase
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
    public function can_count_pattern_occurrences(): void
    {
        // Row 1 text1 is 'this is a test string' - contains 4 occurrences of 't': This, TesT, sTring
        $dql = "SELECT REGEXP_COUNT(t.text1, 't') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(4, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_when_no_match(): void
    {
        // Row 1 text1 is 'this is a test string' - no 'xyz' pattern
        $dql = "SELECT REGEXP_COUNT(t.text1, 'xyz') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }

    #[Test]
    public function can_count_with_start_position(): void
    {
        // Row 1 text1 is 'this is a test string' - starting at position 5, 3 't's remain (in TesT & sTring)
        $dql = "SELECT REGEXP_COUNT(t.text1, 't', 5) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(3, $result[0]['result']);
    }
}
