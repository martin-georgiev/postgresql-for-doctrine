<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg;
use PHPUnit\Framework\Attributes\Test;

final class StringAggTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRING_AGG' => StringAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_values_from_an_entity_field(): void
    {
        $dql = "SELECT STRING_AGG(t.text1, ',') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('this is a test string,lorem ipsum dolor,foo,special,chars;test', $result[0]['result']);
    }
}
