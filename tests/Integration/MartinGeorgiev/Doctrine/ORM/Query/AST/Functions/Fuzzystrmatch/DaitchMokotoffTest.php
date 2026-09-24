<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DaitchMokotoff;
use PHPUnit\Framework\Attributes\Test;

final class DaitchMokotoffTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DAITCH_MOKOTOFF' => DaitchMokotoff::class,
        ];
    }

    #[Test]
    public function returns_the_soundex_codes_from_a_text_literal(): void
    {
        $dql = "SELECT DAITCH_MOKOTOFF('George') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{595000}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_soundex_codes_from_an_entity_field(): void
    {
        $dql = 'SELECT DAITCH_MOKOTOFF(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{344343}', $result[0]['result']);
    }
}
