<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Format;
use PHPUnit\Framework\Attributes\Test;

final class FormatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FORMAT' => Format::class,
        ];
    }

    #[Test]
    public function returns_the_formatted_text_from_text_literals(): void
    {
        $dql = "SELECT FORMAT('Hello %s', 'world') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Hello world', $result[0]['result']);
    }

    #[Test]
    public function returns_the_formatted_text_from_entity_fields(): void
    {
        $dql = "SELECT FORMAT('%s - %s', t.text1, t.text2) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t 
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo - bar', $result[0]['result']);
    }
}
