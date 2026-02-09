<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DaitchMokotoff;
use PHPUnit\Framework\Attributes\Test;

class DaitchMokotoffTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DAITCH_MOKOTOFF' => DaitchMokotoff::class,
        ];
    }

    #[Test]
    public function returns_soundex_codes_as_array(): void
    {
        $dql = "SELECT DAITCH_MOKOTOFF('George') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{595000}', $result[0]['result']);
    }

    #[Test]
    public function can_process_text_field(): void
    {
        $dql = 'SELECT DAITCH_MOKOTOFF(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{344343}', $result[0]['result']);
    }
}
