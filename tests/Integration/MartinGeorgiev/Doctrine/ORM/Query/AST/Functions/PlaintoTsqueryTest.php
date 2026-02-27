<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PlaintoTsquery;
use PHPUnit\Framework\Attributes\Test;

class PlaintoTsqueryTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PLAINTO_TSQUERY' => PlaintoTsquery::class,
        ];
    }

    #[Test]
    public function can_convert_plain_text_to_tsquery(): void
    {
        $dql = "SELECT PLAINTO_TSQUERY('morum ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'morum' & 'lorem'", $result[0]['result']);
    }

    #[Test]
    public function can_convert_plain_text_with_config(): void
    {
        $dql = "SELECT PLAINTO_TSQUERY('english', 'lorem ipsum') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'ipsum' & 'lorem'", $result[0]['result']);
    }

    #[Test]
    public function can_convert_field_value_to_tsquery(): void
    {
        $dql = 'SELECT PLAINTO_TSQUERY(t.text1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame("'dolor' & 'ipsum' & 'lorem'", $result[0]['result']);
    }
}
