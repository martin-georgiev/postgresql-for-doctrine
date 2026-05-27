<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed;
use PHPUnit\Framework\Attributes\Test;

final class XmlIsWellFormedTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_IS_WELL_FORMED' => XmlIsWellFormed::class,
        ];
    }

    #[Test]
    public function checks_xml_is_well_formed_of_literal(): void
    {
        $dql = "SELECT XML_IS_WELL_FORMED('<root>test</root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }

    #[Test]
    public function checks_xml_is_well_formed_with_entity_property(): void
    {
        $dql = 'SELECT XML_IS_WELL_FORMED(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
