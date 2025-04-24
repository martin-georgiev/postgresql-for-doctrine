<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use MartinGeorgiev\Doctrine\DBAL\Types\BaseType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseTypeTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
    }

    /**
     * @test
     */
    public function throws_exception_when_type_name_not_configured(): void
    {
        $type = new class extends BaseType {
            protected const TYPE_NAME = '';
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('no meaningful value for TYPE_NAME constant');

        $type->getName();
    }

    /**
     * @test
     */
    public function throws_exception_when_getting_sql_declaration_with_no_type_name(): void
    {
        $type = new class extends BaseType {
            protected const TYPE_NAME = '';
        };

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Doctrine type defined in class');

        $type->getSQLDeclaration([], $this->platform);
    }

    /**
     * @test
     */
    public function returns_correct_type_name(): void
    {
        $type = new class extends BaseType {
            protected const TYPE_NAME = 'custom_type';
        };

        self::assertEquals('custom_type', $type->getName());
    }

    /**
     * @test
     */
    public function gets_correct_sql_declaration(): void
    {
        $type = new class extends BaseType {
            protected const TYPE_NAME = 'custom_type';
        };

        $this->platform
            ->expects(self::once())
            ->method('getDoctrineTypeMapping')
            ->with('custom_type')
            ->willReturn('CUSTOM_SQL_TYPE');

        $result = $type->getSQLDeclaration([], $this->platform);
        self::assertEquals('CUSTOM_SQL_TYPE', $result);
    }

    /**
     * @test
     */
    public function requires_sql_comment_hint_returns_false(): void
    {
        $type = new class extends BaseType {
            protected const TYPE_NAME = 'custom_type';
        };

        // @phpstan-ignore-next-line Not all Doctrine version like this method as it's deprecated. For now, we ignore the deprecation.
        self::assertFalse($type->requiresSQLCommentHint($this->platform));
    }
}
