<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Array_\CallableThisArrayToAnonymousFunctionRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\EarlyReturn\Rector\Foreach_\ChangeNestedForeachIfsToEarlyContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeIfElseValueAssignToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeNestedIfsToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfContinueToMultiContinueRector;
use Rector\EarlyReturn\Rector\If_\ChangeOrIfReturnToEarlyReturnRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\Return_\PreparedValueToEarlyReturnRector;
use Rector\Php74\Rector\Property\RestoreDefaultNullToNullableTypePropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\ClassMethod\NewInInitializerRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $basePath = __DIR__.'/../../';
    $paths = [
        $basePath.'ci',
        $basePath.'src',
        $basePath.'tests',
    ];
    $rectorConfig->paths($paths);

    $rectorConfig->parallel();
    $rectorConfig->phpstanConfig($basePath.'ci/phpstan/config.neon');
    $rectorConfig->skip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        CallableThisArrayToAnonymousFunctionRector::class,
        FirstClassCallableRector::class,
        InlineConstructorDefaultToPropertyRector::class,
        NewInInitializerRector::class,
        NullToStrictStringFuncCallArgRector::class,
        RestoreDefaultNullToNullableTypePropertyRector::class,
    ]);
    $rectorConfig->importShortClasses(false);
    $rectorConfig->importNames(false); // @todo Enable once Rector introduces better support for function imports.

    $rectorConfig->import(SetList::CODE_QUALITY);
    $rectorConfig->import(SetList::PSR_4);
    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(DoctrineSetList::DOCTRINE_CODE_QUALITY);
    $rectorConfig->import(LevelSetList::UP_TO_PHP_80);

    $rectorConfig->rule(PreparedValueToEarlyReturnRector::class);
    $rectorConfig->rule(ChangeNestedIfsToEarlyReturnRector::class);
    $rectorConfig->rule(ChangeOrIfReturnToEarlyReturnRector::class);
    $rectorConfig->rule(ChangeOrIfContinueToMultiContinueRector::class);
    $rectorConfig->rule(ChangeNestedForeachIfsToEarlyContinueRector::class);
    $rectorConfig->rule(RemoveAlwaysElseRector::class);
    $rectorConfig->rule(ChangeIfElseValueAssignToEarlyReturnRector::class);
};
