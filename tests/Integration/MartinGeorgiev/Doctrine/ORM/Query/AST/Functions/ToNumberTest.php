<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber;
use PHPUnit\Framework\Attributes\Test;

final class ToNumberTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_NUMBER' => ToNumber::class,
        ];
    }

    #[Test]
    public function converts_the_text_to_a_number_from_a_literal(): void
    {
        $dql = "SELECT TO_NUMBER('12,454.8-', '99G999D9S') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('-12454.8', $result[0]['result']);
    }

    #[Test]
    public function rejects_a_null_format_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_NUMBER('12,454.8-', NULL) FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_numeric_first_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_NUMBER(123456, '999D99S') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
