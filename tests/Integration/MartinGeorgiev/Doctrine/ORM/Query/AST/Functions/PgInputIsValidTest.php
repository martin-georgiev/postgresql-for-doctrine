<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PgInputIsValid;
use PHPUnit\Framework\Attributes\Test;

final class PgInputIsValidTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PG_INPUT_IS_VALID' => PgInputIsValid::class,
        ];
    }

    #[Test]
    public function returns_whether_the_input_is_valid_from_a_text_literal(): void
    {
        $dql = "SELECT PG_INPUT_IS_VALID('42', 'integer') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_input_is_valid_from_an_entity_field(): void
    {
        $dql = "SELECT PG_INPUT_IS_VALID(t.text1, 'integer') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
