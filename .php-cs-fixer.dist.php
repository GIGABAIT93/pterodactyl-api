<?php

declare(strict_types=1);

$paths = [__DIR__ . '/src'];
if (is_dir(__DIR__ . '/tests')) {
    $paths[] = __DIR__ . '/tests';
}

$finder = PhpCsFixer\Finder::create()
    ->in($paths)
    ->name('*.php')
    ->ignoreVCS(true)
    ->ignoreDotFiles(true)
    ->exclude(['vendor']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'single_quote' => true,
        'no_extra_blank_lines' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'blank_line_before_statement' => ['statements' => ['return']],
        'phpdoc_align' => false,
        'phpdoc_separation' => false,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
    ]);
