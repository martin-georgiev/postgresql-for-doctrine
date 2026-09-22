<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\Test;

final class StringToArrayTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRING_TO_ARRAY' => StringToArray::class,
        ];
    }

    #[Test]
    public function returns_an_array_from_an_entity_field(): void
    {
        $dql = "SELECT STRING_TO_ARRAY(t.text1, ' ') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);

        $this->assertIsString($result[0]['result']);
        $this->assertMatchesRegularExpression('/^\{.*\}$/', $result[0]['result']);

        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($result[0]['result']);
        $expected = ['this', 'is', 'a', 'test', 'string'];

        $this->assertSame($expected, $parsed);
    }

    #[Test]
    public function returns_an_array_from_a_literal(): void
    {
        $dql = "SELECT STRING_TO_ARRAY('special,chars;test', ',') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);

        $this->assertIsString($result[0]['result']);
        $this->assertMatchesRegularExpression('/^\{.*\}$/', $result[0]['result']);

        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($result[0]['result']);
        $expected = ['special', 'chars;test'];

        $this->assertSame($expected, $parsed);
    }
}
