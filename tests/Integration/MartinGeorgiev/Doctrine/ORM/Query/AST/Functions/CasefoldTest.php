<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Casefold;
use PHPUnit\Framework\Attributes\Test;

class CasefoldTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CASEFOLD' => Casefold::class,
        ];
    }

    #[Test]
    public function can_casefold_a_string(): void
    {
        $dql = "SELECT CASEFOLD('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hello doctrine', $result[0]['result']);
    }

    #[Test]
    public function can_casefold_text_field(): void
    {
        $dql = 'SELECT CASEFOLD(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string', $result[0]['result']);
    }
}
