<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsHeadline;
use PHPUnit\Framework\Attributes\Test;

class TsHeadlineTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TS_HEADLINE' => TsHeadline::class,
        ];
    }

    #[Test]
    public function can_highlight_matching_terms(): void
    {
        $dql = "SELECT TS_HEADLINE(t.text1, TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<b>lorem</b> ipsum dolor', $result[0]['result']);
    }

    #[Test]
    public function can_highlight_with_language_config(): void
    {
        $dql = "SELECT TS_HEADLINE('english', t.text1, TO_TSQUERY('lorem')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<b>lorem</b> ipsum dolor', $result[0]['result']);
    }

    #[Test]
    public function can_highlight_with_custom_options(): void
    {
        $dql = "SELECT TS_HEADLINE(t.text1, TO_TSQUERY('lorem'), 'StartSel=<<, StopSel=>>') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<<lorem>> ipsum dolor', $result[0]['result']);
    }

    #[Test]
    public function can_highlight_with_config_and_options(): void
    {
        $dql = "SELECT TS_HEADLINE('english', t.text1, TO_TSQUERY('english', 'lorem'), 'StartSel=<<, StopSel=>>') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<<lorem>> ipsum dolor', $result[0]['result']);
    }

    #[Test]
    public function can_highlight_literal_document(): void
    {
        $dql = "SELECT TS_HEADLINE('lorem ipsum dolor', TO_TSQUERY('ipsum')) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('lorem <b>ipsum</b> dolor', $result[0]['result']);
    }
}
