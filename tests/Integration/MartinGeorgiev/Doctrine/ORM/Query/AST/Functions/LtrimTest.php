<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltrim;
use PHPUnit\Framework\Attributes\Test;

class LtrimTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LTRIM' => Ltrim::class,
        ];
    }

    #[Test]
    public function trims_leading_spaces_from_literal(): void
    {
        $dql = "SELECT LTRIM('  hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hello', $result[0]['result']);
    }

    #[Test]
    public function trims_specified_leading_characters_from_text_field(): void
    {
        $dql = "SELECT LTRIM(t.text1, 'fobar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
