<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlConcat;
use PHPUnit\Framework\Attributes\Test;

final class XmlConcatTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLCONCAT' => XmlConcat::class,
        ];
    }

    #[Test]
    public function concatenates_two_xml_values(): void
    {
        $dql = "SELECT XMLCONCAT('<a>1</a>', '<b>2</b>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<a>1</a><b>2</b>', $result[0]['result']);
    }

    #[Test]
    public function concatenates_three_xml_values(): void
    {
        $dql = "SELECT XMLCONCAT('<a>hello</a>', '<b>world</b>', '<c>!</c>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<a>hello</a><b>world</b><c>!</c>', $result[0]['result']);
    }
}
