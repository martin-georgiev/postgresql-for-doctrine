<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use PHPUnit\Framework\Attributes\Test;

final class ArrTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function creates_an_array_from_text_literals(): void
    {
        $dql = "SELECT ARR('apple', 'banana') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{apple,banana}', $result[0]['result']);
    }

    #[Test]
    public function creates_an_array_from_an_entity_field(): void
    {
        $dql = 'SELECT ARR(t.id) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{1}', $result[0]['result']);
    }
}
