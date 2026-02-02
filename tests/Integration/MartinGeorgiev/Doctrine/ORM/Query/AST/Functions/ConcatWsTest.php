<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ConcatWs;
use PHPUnit\Framework\Attributes\Test;

class ConcatWsTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONCAT_WS' => ConcatWs::class,
        ];
    }

    #[Test]
    public function can_concat_with_separator(): void
    {
        $dql = "SELECT CONCAT_WS('-', t.text1, t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('foo-bar', $result[0]['result']);
    }

    #[Test]
    public function can_concat_multiple_values(): void
    {
        $dql = "SELECT CONCAT_WS(' ', t.text1, 'extra', t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('foo extra bar', $result[0]['result']);
    }
}
