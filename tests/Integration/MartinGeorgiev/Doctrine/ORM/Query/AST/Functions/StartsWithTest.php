<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith;
use PHPUnit\Framework\Attributes\Test;

class StartsWithTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STARTS_WITH' => StartsWith::class,
        ];
    }

    #[Test]
    public function returns_true_when_string_starts_with_prefix(): void
    {
        $dql = "SELECT STARTS_WITH(t.text1, 'this') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_string_does_not_start_with_prefix(): void
    {
        $dql = "SELECT STARTS_WITH(t.text1, 'xyz') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function is_case_sensitive(): void
    {
        $dql = "SELECT STARTS_WITH(t.text1, 'This') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
