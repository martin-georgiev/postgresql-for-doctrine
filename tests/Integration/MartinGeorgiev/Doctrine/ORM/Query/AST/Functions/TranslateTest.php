<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Translate;
use PHPUnit\Framework\Attributes\Test;

class TranslateTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRANSLATE' => Translate::class,
        ];
    }

    #[Test]
    public function translates_characters_in_literal(): void
    {
        $dql = "SELECT TRANSLATE('hello', 'el', 'ip') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('hippo', $result[0]['result']);
    }

    #[Test]
    public function translates_characters_in_text_field(): void
    {
        $dql = "SELECT TRANSLATE(t.text1, 'foo', 'bar') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('baa', $result[0]['result']);
    }
}
