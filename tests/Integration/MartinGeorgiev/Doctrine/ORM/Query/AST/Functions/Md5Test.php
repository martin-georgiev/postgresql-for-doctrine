<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Md5;
use PHPUnit\Framework\Attributes\Test;

class Md5Test extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MD5' => Md5::class,
        ];
    }

    #[Test]
    public function can_compute_md5_of_a_string(): void
    {
        $dql = "SELECT MD5('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame(\md5('Hello Doctrine'), $result[0]['result']);
    }

    #[Test]
    public function can_compute_md5_of_text_field(): void
    {
        $dql = 'SELECT MD5(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame(\md5('this is a test string'), $result[0]['result']);
    }
}
