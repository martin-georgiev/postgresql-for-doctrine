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
    public function can_trim_spaces_from_both_ends_of_a_literal_string(): void
    {
        $dql = "SELECT BTRIM(' hello ') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hello', $result[0]['result']);
    }

    #[Test]
    public function can_trim_specified_characters_from_both_ends_of_text_field(): void
    {
        $dql = "SELECT BTRIM(t.text1, 'fobar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('', $result[0]['result']);
    }
}
