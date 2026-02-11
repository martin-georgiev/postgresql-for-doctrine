<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Dmetaphone;
use PHPUnit\Framework\Attributes\Test;

class DmetaphoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DMETAPHONE' => Dmetaphone::class,
        ];
    }

    #[Test]
    public function can_compute_primary_double_metaphone_code(): void
    {
        $dql = "SELECT DMETAPHONE('gumbo') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('KMP', $result[0]['result']);
    }

    #[Test]
    public function can_compute_dmetaphone_from_text_field(): void
    {
        $dql = 'SELECT DMETAPHONE(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('0SST', $result[0]['result']);
    }
}
