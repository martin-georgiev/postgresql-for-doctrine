<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Format;
use PHPUnit\Framework\Attributes\Test;

class FormatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FORMAT' => Format::class,
        ];
    }

    #[Test]
    public function can_format_single_argument(): void
    {
        $dql = "SELECT FORMAT('Hello %s', t.text1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('Hello foo', $result[0]['result']);
    }

    #[Test]
    public function can_format_multiple_arguments(): void
    {
        $dql = "SELECT FORMAT('%s - %s', t.text1, t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('foo - bar', $result[0]['result']);
    }
}
