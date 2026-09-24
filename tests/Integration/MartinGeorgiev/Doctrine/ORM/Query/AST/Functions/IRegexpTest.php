<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp;
use PHPUnit\Framework\Attributes\Test;

final class IRegexpTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IREGEXP' => IRegexp::class,
        ];
    }

    #[Test]
    public function returns_whether_the_pattern_matches_from_an_entity_field(): void
    {
        $dql = "SELECT IREGEXP(t.text1, 'test.*string') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_pattern_matches_from_a_text_literal(): void
    {
        $dql = "SELECT IREGEXP('this is a test string', 'TEST.*STRING') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
