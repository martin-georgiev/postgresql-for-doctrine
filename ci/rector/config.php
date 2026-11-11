<?php

declare(strict_types=1);

use Ci\MartinGeorgiev\Rector\MethodOrderRector;
use Ci\MartinGeorgiev\Rector\ParentByNamespaceRector;
use Ci\MartinGeorgiev\Rector\TestDoubleIntersectionVarRector;
use Ci\MartinGeorgiev\Rector\VarTagByConstantValueRector;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use PHPUnit\Framework\MockObject\MockObject;
use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\If_\ExplicitNullableObjectInstanceRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Privatization\Rector\Class_\FinalizeTestCaseClassRector;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

require_once __DIR__.'/rules/MethodOrderRector.php';

require_once __DIR__.'/rules/ParentByNamespaceRector.php';

require_once __DIR__.'/rules/TestDoubleIntersectionVarRector.php';

require_once __DIR__.'/rules/VarTagByConstantValueRector.php';

$basePath = __DIR__.'/../../';

return RectorConfig::configure()
    ->withPaths([
        $basePath.'ci',
        $basePath.'fixtures',
        $basePath.'src',
        $basePath.'tests',
    ])
    ->withCache($basePath.'var/cache/rector/')
    ->withParallel()
    ->withPHPStanConfigs([$basePath.'ci/phpstan/config.neon'])
    ->withComposerBased(
        doctrine: true,
        phpunit: true,
    )
    ->withSets([
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
    ])
    ->withRules([
        FinalizeTestCaseClassRector::class,
        PreferPHPUnitThisCallRector::class,
        VarTagByConstantValueRector::class,
    ])
    // the DBAL types translate value object failures with catch (\InvalidArgumentException);
    // a member of this namespace that escapes it surfaces from the wrong layer
    ->withConfiguredRule(ParentByNamespaceRector::class, [
        'MartinGeorgiev\\Doctrine\\DBAL\\Types\\Exceptions\\' => ConversionException::class,
        'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\Exceptions\\' => InvalidArgumentException::class,
        'MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\' => FunctionNode::class,
        'MartinGeorgiev\\Utils\\Exception\\' => InvalidArgumentException::class,
    ])
    // a DBAL type reads as the write path then the read path, the order the rules ask for
    ->withConfiguredRule(MethodOrderRector::class, [
        'convertToDatabaseValue',
        'convertToPHPValue',
    ])
    // a bare MockObject hides what was doubled; the stock rule is bonded to PHPUnit 11 and reverses the order
    ->withConfiguredRule(TestDoubleIntersectionVarRector::class, [
        MockObject::class => ['createMock', 'getMockBuilder'],
    ])
    ->withSkip([
        // a bad column option is a mapping mistake, not a per-value conversion failure
        ParentByNamespaceRector::class => [
            $basePath.'src/MartinGeorgiev/Doctrine/DBAL/Types/Exceptions/InvalidSpatialColumnDeclarationException.php',
            $basePath.'src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Exception',
        ],
        // skip as it breaks support for legacy Lexer discovery on older Doctrine versions
        ExplicitNullableObjectInstanceRector::class => [
            $basePath.'src/MartinGeorgiev/Utils/DoctrineLexer.php',
        ],
        // skip as it breaks intentionally looser milliseconds check when dealing with timestamps
        UseIdenticalOverEqualWithSameTypeRector::class => [
            $basePath.'/src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Interval.php',
        ],
        // skip as it breaks support for Lexer constants on older Doctrine versions
        RenameClassConstFetchRector::class => [
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseAggregateFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseOrderedSetAggregateFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseVariadicFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Cast.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/CompositeField.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Filter.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/JsonGetField.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Over.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/StringAgg.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/XmlAgg.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/XmlPi.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Traits/DistinctableTrait.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Traits/OrderableTrait.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Traits/WindowSpecificationTrait.php',
        ],
    ])
    ->withImportNames(
        importNames: false,
        importDocBlockNames: false,
        importShortClasses: false,
    );
