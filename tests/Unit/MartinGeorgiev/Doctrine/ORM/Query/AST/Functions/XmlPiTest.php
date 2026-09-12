<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidXmlPiTargetException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class XmlPiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLPI' => XmlPi::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'creates processing instruction from target only' => 'SELECT xmlpi(NAME "foo") AS sclr_0 FROM ContainsTexts c0_',
            'creates processing instruction from target and content field' => 'SELECT xmlpi(NAME "foo", c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'creates processing instruction from target containing a hyphen' => 'SELECT xmlpi(NAME "php-app") AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'creates processing instruction from target only' => \sprintf("SELECT XMLPI('foo') FROM %s e", ContainsTexts::class),
            'creates processing instruction from target and content field' => \sprintf("SELECT XMLPI('foo', e.text2) FROM %s e", ContainsTexts::class),
            'creates processing instruction from target containing a hyphen' => \sprintf("SELECT XMLPI('php-app') FROM %s e", ContainsTexts::class),
        ];
    }

    #[DataProvider('provideInjectionPayloads')]
    #[Test]
    public function escapes_target_into_a_single_quoted_identifier(string $target, string $expectedSql): void
    {
        $dql = \sprintf("SELECT XMLPI('%s') FROM %s e", $target, ContainsTexts::class);
        $this->assertSqlFromDql($expectedSql, $dql);
    }

    /**
     * @return array<string, array{target: string, expectedSql: string}>
     */
    public static function provideInjectionPayloads(): array
    {
        return [
            'comma smuggling a subquery' => [
                'target' => 'php, (SELECT version())',
                'expectedSql' => 'SELECT xmlpi(NAME "php, (SELECT version())") AS sclr_0 FROM ContainsTexts c0_',
            ],
            'double quote breaking out of the identifier' => [
                'target' => 'php", (SELECT version()) AS "x',
                'expectedSql' => 'SELECT xmlpi(NAME "php"", (SELECT version()) AS ""x") AS sclr_0 FROM ContainsTexts c0_',
            ],
        ];
    }

    #[Test]
    public function throws_exception_for_empty_target(): void
    {
        $this->expectException(InvalidXmlPiTargetException::class);

        $dql = \sprintf("SELECT XMLPI('') FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
