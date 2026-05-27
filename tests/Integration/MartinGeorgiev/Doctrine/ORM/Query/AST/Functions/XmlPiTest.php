<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi;
use PHPUnit\Framework\Attributes\Test;

final class XmlPiTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLPI' => XmlPi::class,
        ];
    }

    #[Test]
    public function creates_xmlpi_from_literal(): void
    {
        $dql = "SELECT XMLPI('foo') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<?foo?>', $result[0]['result']);
    }

    #[Test]
    public function creates_xmlpi_with_content_from_literals(): void
    {
        $dql = "SELECT XMLPI('php', 'echo \"hello world\";') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<?php echo "hello world";?>', $result[0]['result']);
    }

    #[Test]
    public function creates_xmlpi_with_entity_property(): void
    {
        $dql = 'SELECT XMLPI(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<?bar?>', $result[0]['result']);
    }
}
