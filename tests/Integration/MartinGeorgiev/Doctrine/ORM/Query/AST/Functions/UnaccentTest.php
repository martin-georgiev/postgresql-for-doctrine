<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent;
use PHPUnit\Framework\Attributes\Test;

final class UnaccentTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensurePostgresExtensionInSchema('unaccent');
    }

    protected function getStringFunctions(): array
    {
        return [
            'UNACCENT' => Unaccent::class,
        ];
    }

    #[Test]
    public function removes_accents_from_text(): void
    {
        $dql = "SELECT UNACCENT('café') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('cafe', $result[0]['result']);
    }

    #[Test]
    public function removes_multiple_accents(): void
    {
        $dql = "SELECT UNACCENT('résumé') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('resume', $result[0]['result']);
    }

    #[Test]
    public function returns_same_text_when_no_accents(): void
    {
        $dql = 'SELECT UNACCENT(t.text1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo', $result[0]['result']);
    }
}
