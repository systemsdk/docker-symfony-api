<?php

declare(strict_types=1);

/** @noinspection PhpUndefinedNamespaceInspection */
/** @noinspection PhpUndefinedClassInspection */

return [
    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony"
    |
    */
    'preset' => 'symfony',
    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */
    'exclude' => [
        'bin',
        'config',
        'docker',
        'docs',
        'public',
        'reports',
        'migrations',
        'templates',
        'tests',
        'tools',
        'translations',
        'var',
        'vendor',
    ],
    'add' => [
    ],
    'remove' => [
        //  ExampleInsight::class,
        NunoMaduro\PhpInsights\Domain\Insights\Composer\ComposerMustBeValid::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        ObjectCalisthenics\Sniffs\NamingConventions\NoSetterSniff::class,
        PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousExceptionNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousInterfaceNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\SuperfluousTraitNamingSniff::class,
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class,
        SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff::class,
        SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff ::class,
        SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff::class,
    ],
    'config' => [
        SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class => [
            'maxLinesLength' => 50,
        ],
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 140,
            'ignoreComments' => true,
            'exclude' => [
                'src/General/Application/Rest/Interfaces/RestResourceInterface.php',
            ],
        ],
        PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff::class => [
            'spacing' => 0,
        ],
        PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class => [
            'spacing' => 0,
        ],
        PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class => [
            'space' => 'none', // possible values ['single', 'none'],
        ],
        PhpCsFixer\Fixer\Import\OrderedImportsFixer::class => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha', // possible values ['alpha', 'length', 'none']
        ],
        PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer::class => [
            'space' => 'none', // possible values ['none', 'single']
        ],
        PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class => [
            'operators' => [
                '&' => 'align',
            ],
        ],
        PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff::class => [
            'exclude' => [
                'src/General/Application/Decorator/StopwatchDecorator.php',
            ],
        ],
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class => [
            'exclude' => [
                'src/General/Transport/ValueResolver/LoggedInUserValueResolver.php',
                'src/General/Transport/ValueResolver/RestDtoValueResolver.php',
                'src/General/Transport/AutoMapper/RestRequestMapper.php',
                'src/General/Application/Decorator/StopwatchDecorator.php',
                'src/General/Domain/Doctrine/DBAL/Types/EnumType.php',
                'src/General/Transport/Rest/Traits/Methods/RestMethodProcessCriteria.php',
                'src/General/Application/Rest/Traits/RestResourceCount.php',
                'src/General/Application/Rest/Traits/RestResourceCreate.php',
                'src/General/Application/Rest/Traits/RestResourceDelete.php',
                'src/General/Application/Rest/Traits/RestResourceFind.php',
                'src/General/Application/Rest/Traits/RestResourceFindOne.php',
                'src/General/Application/Rest/Traits/RestResourceFindOneBy.php',
                'src/General/Application/Rest/Traits/RestResourceIds.php',
                'src/General/Application/Rest/Traits/RestResourcePatch.php',
                'src/General/Application/Rest/Traits/RestResourceSave.php',
                'src/General/Application/Rest/Traits/RestResourceUpdate.php',
                'src/ApiKey/Application/Security/Authenticator/ApiKeyAuthenticator.php',
                'src/User/Application/Security/Handler/TranslatedAuthenticationFailureHandler.php',
                'src/ApiKey/Application/Security/Provider/ApiKeyUserProvider.php',
                'src/User/Application/Security/Voter/IsUserHimselfVoter.php',
                'src/Tool/Application/Validator/Constraints/LanguageValidator.php',
                'src/Tool/Application/Validator/Constraints/LocaleValidator.php',
                'src/Tool/Application/Validator/Constraints/TimezoneValidator.php',
                'src/User/Application/Validator/Constraints/UniqueEmailValidator.php',
                'src/User/Application/Validator/Constraints/UniqueUsernameValidator.php',
                'src/Tool/Transport/MessageHandler/TestHandler.php',
            ],
        ],
        SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff::class => [
            'searchAnnotations' => true,
        ],
        SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class => [
            'linesCountBeforeDeclare' => 1,
            'linesCountAfterDeclare' => 1,
            'spacesCountAroundEqualsSign' => 1,
        ],
        SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class => [
            'linesCountBeforeFirstUse' => 1,
            'linesCountBetweenUseTypes' => 1,
            'linesCountAfterLastUse' => 1,
        ],
    ],
];
