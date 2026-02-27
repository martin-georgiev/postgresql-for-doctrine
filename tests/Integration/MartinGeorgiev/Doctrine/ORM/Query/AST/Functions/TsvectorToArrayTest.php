<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsvectorToArray;
use MartinGeorgiev\Utils\PostgresArrayToPHPArrayTransformer;
use PHPUnit\Framework\Attributes\Test;

class TsvectorToArrayTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
            'TSVECTOR_TO_ARRAY' => TsvectorToArray::class,
        ];
    }

    #[Test]
    public function can_convert_tsvector_to_array_of_lexemes(): void
    {
        $dql = 'SELECT TSVECTOR_TO_ARRAY(TO_TSVECTOR(t.text1)) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);

        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($result[0]['result']);
        $this->assertSame(['dolor', 'ipsum', 'lorem'], $parsed);
    }

    #[Test]
    public function can_convert_literal_tsvector_to_array_of_lexemes(): void
    {
        $dql = "SELECT TSVECTOR_TO_ARRAY(TO_TSVECTOR('borum morum forum')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);

        $parsed = PostgresArrayToPHPArrayTransformer::transformPostgresArrayToPHPArray($result[0]['result']);
        $this->assertSame(['borum', 'morum', 'forum'], $parsed);
    }
}
