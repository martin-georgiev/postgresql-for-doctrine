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
    public function returns_true_for_a_well_formed_literal(): void
    {
        $dql = "SELECT XML_IS_WELL_FORMED('<root>test</root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_true_for_a_well_formed_entity_field(): void
    {
        $dql = 'SELECT XML_IS_WELL_FORMED(t.text1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
