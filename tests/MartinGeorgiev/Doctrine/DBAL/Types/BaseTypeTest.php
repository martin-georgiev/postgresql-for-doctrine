<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\DBAL\Types;

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

    /**
     * @var BaseType&MockObject
     */
    private MockObject $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);

        $this->fixture = $this->getMockBuilder(BaseType::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @test
     */
    public function throws_logic_exception_when_no_name_is_set(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/Doctrine type defined in class .+ has no meaningful value for TYPE_NAME constant/');

        $this->fixture->getSQLDeclaration([], $this->platform);
    }
}
