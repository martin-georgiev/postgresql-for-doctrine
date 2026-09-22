<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat;
use PHPUnit\Framework\Attributes\Test;

final class StrConcatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STR_CONCAT' => StrConcat::class,
        ];
    }

    #[Test]
    public function returns_the_concatenation_from_entity_fields(): void
    {
        $dql = 'SELECT STR_CONCAT(t.text1, t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foobar', $result[0]['result']);
    }

    #[Test]
    public function returns_the_concatenation_from_an_entity_field_and_a_literal(): void
    {
        $dql = "SELECT STR_CONCAT(t.text1, ' suffix') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo suffix', $result[0]['result']);
    }
}
