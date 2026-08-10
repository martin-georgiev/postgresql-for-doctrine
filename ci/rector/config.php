<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Equal\UseIdenticalOverEqualWithSameTypeRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveParentDelegatingConstructorRector;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Naming\Rector\Class_\RenamePropertyToMatchTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\Class_\PreferPHPUnitThisCallRector;
use Rector\Renaming\Rector\ClassConstFetch\RenameClassConstFetchRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

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
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::NAMING,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,
        SetList::CODING_STYLE,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
    ])
    ->withRules([
        PreferPHPUnitThisCallRector::class,
    ])
    ->withSkip([
        // skip as it breaks support for legacy Lexer discovery on older Doctrine versions
        FlipTypeControlToUseExclusiveTypeRector::class => [
            $basePath.'src/MartinGeorgiev/Utils/DoctrineLexer.php',
        ],
        // skip as it breaks intentionally looser milliseconds check when dealing with timestamps
        UseIdenticalOverEqualWithSameTypeRector::class => [
            $basePath.'/src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Interval.php',
        ],
        // skip as it removes the intended type narrowing for constructor arguments
        RemoveParentDelegatingConstructorRector::class => [
            $basePath.'src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/BaseTimestampRange.php',
        ],
        // skip as it breaks support for Lexer constants on older Doctrine versions
        RenameClassConstFetchRector::class => [
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseAggregateFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/BaseVariadicFunction.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Cast.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/CompositeField.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/JsonGetField.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/StringAgg.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/XmlAgg.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/XmlPi.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Traits/DistinctableTrait.php',
            $basePath.'/src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions/Traits/OrderableTrait.php',
        ],
        // skip globally as it eliminates self-documenting code where naming is intentionally different to capture specific intention
        RenamePropertyToMatchTypeRector::class,
    ])
    ->withImportNames(
        importNames: false,
        importDocBlockNames: false,
        importShortClasses: false,
        removeUnusedImports: true,
    );
