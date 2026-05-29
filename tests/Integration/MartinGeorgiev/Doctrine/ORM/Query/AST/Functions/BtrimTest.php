<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Btrim;
use PHPUnit\Framework\Attributes\Test;

class BtrimTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BTRIM' => Btrim::class,
        ];
    }

    #[Test]
    public function trims_spaces_from_both_ends_of_literal(): void
    {
        $dql = "SELECT BTRIM(' hello ') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hello', $result[0]['result']);
    }

    #[Test]
    public function trims_specified_characters_from_both_ends_of_text_field(): void
    {
        $dql = "SELECT BTRIM(t.text1, 'fobar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
