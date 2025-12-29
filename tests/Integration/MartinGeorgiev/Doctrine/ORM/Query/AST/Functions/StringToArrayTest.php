<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;
use PHPUnit\Framework\Attributes\Test;

class StringToArrayTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRING_TO_ARRAY' => StringToArray::class,
        ];
    }

    #[Test]
    public function can_split_string_into_array(): void
    {
        $dql = "SELECT STRING_TO_ARRAY(t.text1, ' ') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('this', $result[0]['result']);
        $this->assertStringContainsString('test', $result[0]['result']);
    }

    #[Test]
    public function can_split_by_comma_delimiter(): void
    {
        $dql = "SELECT STRING_TO_ARRAY(t.text1, ',') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 4";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('special', $result[0]['result']);
    }
}
