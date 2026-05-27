<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlComment;
use PHPUnit\Framework\Attributes\Test;

final class XmlCommentTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLCOMMENT' => XmlComment::class,
        ];
    }

    #[Test]
    public function creates_xmlcomment_from_literal(): void
    {
        $dql = "SELECT XMLCOMMENT('hello') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<!--hello-->', $result[0]['result']);
    }

    #[Test]
    public function creates_xmlcomment_with_entity_property(): void
    {
        $dql = 'SELECT XMLCOMMENT(t.text2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts t
                WHERE t.id = 3';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<!--bar-->', $result[0]['result']);
    }
}
