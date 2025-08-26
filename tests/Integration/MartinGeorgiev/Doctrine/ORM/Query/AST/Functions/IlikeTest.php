<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike;
use PHPUnit\Framework\Attributes\Test;

class IlikeTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ILIKE' => Ilike::class,
        ];
    }

    #[Test]
    public function returns_true_for_case_insensitive_matching_string(): void
    {
        $dql = "SELECT ILIKE(t.text1, '%test%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_for_case_insensitive_matching_with_different_case(): void
    {
        $dql = "SELECT ILIKE(t.text1, '%TEST%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_for_non_matching_string(): void
    {
        $dql = "SELECT ILIKE(t.text1, 'nonexistent') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_second_text_field(): void
    {
        $dql = "SELECT ILIKE(t.text2, '%another%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_matching_partial_string(): void
    {
        $dql = "SELECT ILIKE(t.text1, '%test%') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
