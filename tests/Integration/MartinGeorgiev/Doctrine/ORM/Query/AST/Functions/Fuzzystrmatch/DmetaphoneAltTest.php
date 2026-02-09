<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DmetaphoneAlt;
use PHPUnit\Framework\Attributes\Test;

class DmetaphoneAltTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DMETAPHONE_ALT' => DmetaphoneAlt::class,
        ];
    }

    #[Test]
    public function can_compute_alternate_double_metaphone_code(): void
    {
        $dql = "SELECT DMETAPHONE_ALT('gumbo') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('KMP', $result[0]['result']);
    }

    #[Test]
    public function can_compute_dmetaphone_alt_from_text_field(): void
    {
        $dql = 'SELECT DMETAPHONE_ALT(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('TSST', $result[0]['result']);
    }
}
