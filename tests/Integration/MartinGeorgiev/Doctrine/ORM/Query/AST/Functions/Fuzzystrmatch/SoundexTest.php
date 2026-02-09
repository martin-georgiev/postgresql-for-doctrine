<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Soundex;
use PHPUnit\Framework\Attributes\Test;

class SoundexTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SOUNDEX' => Soundex::class,
        ];
    }

    #[Test]
    public function can_compute_soundex_code(): void
    {
        $dql = "SELECT SOUNDEX('Anne') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('A500', $result[0]['result']);
    }

    #[Test]
    public function can_compute_soundex_from_text_field(): void
    {
        $dql = 'SELECT SOUNDEX(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('T223', $result[0]['result']);
    }
}
