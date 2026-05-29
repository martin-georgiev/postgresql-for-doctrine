<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Initcap;
use PHPUnit\Framework\Attributes\Test;

class InitcapTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INITCAP' => Initcap::class,
        ];
    }

    #[Test]
    public function capitalizes_first_letter_of_each_word_in_literal(): void
    {
        $dql = "SELECT INITCAP('hello world') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Hello World', $result[0]['result']);
    }

    #[Test]
    public function capitalizes_first_letter_of_each_word_in_text_field(): void
    {
        $dql = 'SELECT INITCAP(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Foo', $result[0]['result']);
    }
}
