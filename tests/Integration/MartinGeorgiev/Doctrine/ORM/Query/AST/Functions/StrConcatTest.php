<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat;
use PHPUnit\Framework\Attributes\Test;

class StrConcatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STR_CONCAT' => StrConcat::class,
        ];
    }

    #[Test]
    public function can_concatenate_two_columns(): void
    {
        $dql = 'SELECT STR_CONCAT(t.text1, t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foobar', $result[0]['result']);
    }

    #[Test]
    public function can_concatenate_column_with_literal(): void
    {
        $dql = "SELECT STR_CONCAT(t.text1, ' suffix') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo suffix', $result[0]['result']);
    }
}
