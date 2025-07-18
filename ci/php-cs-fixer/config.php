<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$basePath = __DIR__.'/../../';

$finder = Finder::create()
    ->in($basePath.'ci')
    ->in($basePath.'fixtures')
    ->in($basePath.'src')
    ->in($basePath.'tests');

$config = new Config();

return $config
    ->setRules(
        [
            '@PSR2' => true,
            '@PHP71Migration' => true,
            '@DoctrineAnnotation' => true,
            '@PhpCsFixer' => true,
            'align_multiline_comment' => false,
            'array_syntax' => ['syntax' => 'short'],
            'backtick_to_shell_exec' => true,
            'blank_line_before_statement' => ['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'include', 'include_once', 'require', 'require_once', 'return', 'switch', 'throw', 'try']],
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
            'string_implicit_backslashes' => false,
            'yoda_style' => false,
        ]
    )
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder);
