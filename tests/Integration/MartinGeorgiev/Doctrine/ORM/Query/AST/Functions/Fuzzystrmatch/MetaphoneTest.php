<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Metaphone;
use PHPUnit\Framework\Attributes\Test;

class MetaphoneTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'METAPHONE' => Metaphone::class,
        ];
    }

    #[Test]
    public function can_compute_metaphone_code(): void
    {
        $dql = "SELECT METAPHONE('GUMBO', 4) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('KM', $result[0]['result']);
    }

    #[Test]
    public function can_compute_metaphone_from_text_field(): void
    {
        $dql = 'SELECT METAPHONE(t.text1, 10) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('0SSTSTSTRN', $result[0]['result']);
    }
}
