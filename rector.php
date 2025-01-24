<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPhpSets(php84: true)
    ->withAttributesSets(phpunit: true)
    ->withRules([
        Rector\CodeQuality\Rector\Ternary\ArrayKeyExistsTernaryThenValueToCoalescingRector::class,
        Rector\CodeQuality\Rector\NullsafeMethodCall\CleanupUnneededNullsafeOperatorRector::class,
        Rector\CodeQuality\Rector\ClassMethod\InlineArrayReturnAssignRector::class,
        Rector\CodeQuality\Rector\Ternary\UnnecessaryTernaryExpressionRector::class,
        Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictFluentReturnRector::class,
        Rector\Php80\Rector\Class_\StringableForToStringRector::class,
        Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector::class,
        Rector\CodingStyle\Rector\Closure\StaticClosureRector::class,
        Rector\DeadCode\Rector\Node\RemoveNonExistingVarAnnotationRector::class,
        Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\BoolReturnTypeFromBooleanStrictReturnsRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\NumericReturnTypeFromStrictScalarReturnsRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector::class,
        Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector::class,
        Rector\CodeQuality\Rector\Foreach_\ForeachItemsAssignToEmptyArrayToAssignRector::class,
        Rector\CodeQuality\Rector\Foreach_\ForeachToInArrayRector::class,
        Rector\CodeQuality\Rector\BooleanAnd\RemoveUselessIsObjectCheckRector::class,
    ])
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withTypeCoverageLevel(0);
