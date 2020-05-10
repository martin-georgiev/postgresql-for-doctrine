<?php

declare(strict_types=1);

$rootDirectory = __DIR__.'/../../';

$finder = PhpCsFixer\Finder::create()
    ->in($rootDirectory.'/src')
    ->in($rootDirectory.'/tests');

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PSR2' => true,
            '@PHP56Migration' => true,
            '@PHP70Migration' => true,
            '@PHP71Migration' => true,
            '@DoctrineAnnotation' => true,
            '@PhpCsFixer' => true,
            'align_multiline_comment' => false,
            'array_syntax' => ['syntax' => 'short'],
            'backtick_to_shell_exec' => true,
            'blank_line_before_statement' => ['statements' => ['break', 'declare', 'continue', 'declare', 'die', 'do', 'exit', 'return', 'throw', 'try', 'while']],
            'cast_spaces' => ['space' => 'single'],
            'concat_space' => ['spacing' => 'none'],
            'date_time_immutable' => true,
            'declare_equal_normalize' => ['space' => 'none'],
            'increment_style' => ['style' => 'post'],
            'linebreak_after_opening_tag' => true,
            'list_syntax' => ['syntax' => 'short'],
            'mb_str_functions' => false,
            'method_chaining_indentation' => true,
            'multiline_whitespace_before_semicolons' => false,
            'native_function_invocation' => ['include' => ['@all']],
            'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
            'non_printable_character' => false,
            'ordered_class_elements' => ['order' => ['use_trait', 'constant', 'property', 'construct', 'magic', 'method']],
            'php_unit_internal_class' => false,
            'php_unit_method_casing' => ['case' => 'snake_case'],
            'php_unit_test_class_requires_covers' => false,
            'phpdoc_types_order' => ['null_adjustment' => 'always_last'],
            'simplified_null_return' => false,
            'single_line_comment_style' => false,
            'static_lambda' => true,
            'yoda_style' => false,
        ]
    )
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setIndent("    ")
    ->setLineEnding("\n")
    ->setFinder($finder);
