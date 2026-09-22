<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ConcatWs;
use PHPUnit\Framework\Attributes\Test;

final class ConcatWsTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CONCAT_WS' => ConcatWs::class,
        ];
    }

    #[Test]
    public function returns_the_concatenated_text_from_text_literals(): void
    {
        $dql = "SELECT CONCAT_WS('-', 'foo', 'bar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo-bar', $result[0]['result']);
    }

    #[Test]
    public function returns_the_concatenated_text_from_entity_fields(): void
    {
        $dql = "SELECT CONCAT_WS(' ', t.text1, 'extra', t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('foo extra bar', $result[0]['result']);
    }
}
