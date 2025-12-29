<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha224;
use PHPUnit\Framework\Attributes\Test;

class Sha224Test extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SHA224' => Sha224::class,
        ];
    }

    #[Test]
    public function can_compute_sha224_of_a_string(): void
    {
        $dql = "SELECT SHA224('Hello Doctrine') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $byteaResult = $result[0]['result'];
        if (\is_resource($byteaResult)) {
            $byteaResult = \stream_get_contents($byteaResult);
        }

        $this->assertSame('dda9aebaec5f0dacba4de19f87a39134a257c4bbbc360b4a7950ce77', \bin2hex((string) $byteaResult));
    }

    #[Test]
    public function can_compute_sha224_of_text_field(): void
    {
        $dql = 'SELECT SHA224(t.text1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $byteaResult = $result[0]['result'];
        if (\is_resource($byteaResult)) {
            $byteaResult = \stream_get_contents($byteaResult);
        }

        $this->assertSame('e07fa407e0e2d64843e0d3e37f8f642dcfd8b47073580e5d27be12be', \bin2hex((string) $byteaResult));
    }
}
