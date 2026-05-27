<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedContent;
use PHPUnit\Framework\Attributes\Test;

final class XmlIsWellFormedContentTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED_CONTENT' => XmlIsWellFormedContent::class,
        ];
    }

    #[Test]
    public function checks_xml_is_well_formed_content_of_literal(): void
    {
        $dql = "SELECT XML_IS_WELL_FORMED_CONTENT('<root>test</root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function checks_xml_is_well_formed_content_with_entity_property(): void
    {
        $dql = 'SELECT XML_IS_WELL_FORMED_CONTENT(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
