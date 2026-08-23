<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\SingleLineThrowFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\Operator\IncrementStyleFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocAnnotationRemoveFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\TypesSpacesFixer;
use Symplify\CodingStandard\Fixer\Annotation\RemovePropertyVariableNameDescriptionFixer;
use Symplify\CodingStandard\Fixer\Spacing\MethodChainingNewlineFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPreparedSets(psr12: true, common: true, cleanCode: true)
    ->withConfiguredRule(
        IncrementStyleFixer::class,
        [
            'style' => 'post',
        ],
    )
    ->withConfiguredRule(
        YodaStyleFixer::class,
        [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    )
    ->withConfiguredRule(
        ConcatSpaceFixer::class,
        [
            'spacing' => 'one',
        ],
    )
    ->withConfiguredRule(
        CastSpacesFixer::class,
        [
            'space' => 'none',
        ],
    )
    ->withConfiguredRule(
        OrderedImportsFixer::class,
        [
            'imports_order' => ['class', 'function', 'const'],
        ],
    )
    ->withConfiguredRule(
        NoSuperfluousPhpdocTagsFixer::class,
        [
            'remove_inheritdoc' => false,
            'allow_mixed' => true,
            'allow_unused_params' => false,
        ],
    )
    ->withConfiguredRule(
        DeclareEqualNormalizeFixer::class,
        [
            'space' => 'none',
        ],
    )
    ->withConfiguredRule(
        BlankLineBeforeStatementFixer::class,
        [
            'statements' => ['continue', 'declare', 'return', 'throw', 'try'],
        ],
    )
    ->withConfiguredRule(
        BinaryOperatorSpacesFixer::class,
        [
            'operators' => ['&' => 'align'],
        ],
    )
    ->withConfiguredRule(
        // https://github.com/nunomaduro/phpinsights/blob/master/docs/insights/style.md#no-extra-blank-lines---
        NoExtraBlankLinesFixer::class,
        [
            'tokens' =>
                [
                    'break',
                    'case',
                    'continue',
                    'curly_brace_block',
                    'default',
                    'extra',
                    'parenthesis_brace_block',
                    'return',
                    'square_brace_block',
                    'switch',
                    'throw',
                    //'use',
                    'use_trait',
                ],
        ],
    )
    ->withConfiguredRule(
        ClassDefinitionFixer::class,
        [
            'multi_line_extends_each_single_line' => true,
        ],
    )
    ->withConfiguredRule(TypesSpacesFixer::class, [
        'space' => 'none',
        'space_multiple_catch' => 'single',
    ])
    ->withSkip([
        BlankLineAfterOpeningTagFixer::class => null,
        ClassAttributesSeparationFixer::class => null,
        GeneralPhpdocAnnotationRemoveFixer::class => null,
        MethodChainingIndentationFixer::class => null,
        MethodChainingNewlineFixer::class => null,
        NoMultilineWhitespaceAroundDoubleArrowFixer::class => null,
        NotOperatorWithSuccessorSpaceFixer::class => null,
        PhpdocNoPackageFixer::class => null,
        RemovePropertyVariableNameDescriptionFixer::class => null,
        SingleLineThrowFixer::class => null,
    ]);
