<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmltext;
use PHPUnit\Framework\Attributes\Test;

final class XmltextTest extends TextTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'XMLTEXT function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XMLTEXT' => Xmltext::class,
        ];
    }

    #[Test]
    public function creates_xmltext_from_literal(): void
    {
        $dql = "SELECT XMLTEXT('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('hello', $result[0]['result']);
    }

    #[Test]
    public function creates_xmltext_with_entity_property(): void
    {
        $dql = 'SELECT XMLTEXT(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('bar', $result[0]['result']);
    }
}
