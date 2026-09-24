<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Dmetaphone;
use PHPUnit\Framework\Attributes\Test;

final class DmetaphoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DMETAPHONE' => Dmetaphone::class,
        ];
    }

    #[Test]
    public function returns_the_primary_double_metaphone_code_from_a_text_literal(): void
    {
        $dql = "SELECT DMETAPHONE('gumbo') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('KMP', $result[0]['result']);
    }

    #[Test]
    public function returns_the_primary_double_metaphone_code_from_an_entity_field(): void
    {
        $dql = 'SELECT DMETAPHONE(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('0SST', $result[0]['result']);
    }
}
