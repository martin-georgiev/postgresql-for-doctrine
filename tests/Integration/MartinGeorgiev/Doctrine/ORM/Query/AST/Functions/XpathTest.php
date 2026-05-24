<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath;
use PHPUnit\Framework\Attributes\Test;

class XpathTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XPATH' => Xpath::class,
        ];
    }

    #[Test]
    public function can_extract_xml_nodes(): void
    {
        $dql = "SELECT XPATH('//child/text()', '<root><child>hello</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }

    #[Test]
    public function can_return_empty_when_no_match(): void
    {
        $dql = "SELECT XPATH('//missing', '<root><child>hello</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
