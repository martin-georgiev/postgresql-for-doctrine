<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlcomment;
use PHPUnit\Framework\Attributes\Test;

class XmlcommentTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLCOMMENT' => Xmlcomment::class,
        ];
    }

    #[Test]
    public function can_create_xml_comment_from_literal(): void
    {
        $dql = "SELECT XMLCOMMENT('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<!--hello-->', $result[0]['result']);
    }

    #[Test]
    public function can_create_xml_comment_from_field(): void
    {
        $dql = 'SELECT XMLCOMMENT(t.text2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<!--bar-->', $result[0]['result']);
    }
}
