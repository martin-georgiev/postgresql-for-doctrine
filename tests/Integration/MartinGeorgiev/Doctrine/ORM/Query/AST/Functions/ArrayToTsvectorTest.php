<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;
use PHPUnit\Framework\Attributes\Test;

class ArrayToTsvectorTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_TSVECTOR' => ArrayToTsvector::class,
            'STRING_TO_ARRAY' => StringToArray::class,
        ];
    }

    #[Test]
    public function can_convert_array_to_tsvector(): void
    {
        $dql = "SELECT ARRAY_TO_TSVECTOR(STRING_TO_ARRAY(t.text1, ' ')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'ipsum' 'lorem'", $result[0]['result']);
    }

    #[Test]
    public function can_convert_literal_array_to_tsvector(): void
    {
        $dql = "SELECT ARRAY_TO_TSVECTOR(ARRAY('lorem', 'dolor')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' 'lorem'", $result[0]['result']);
    }
}
