<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/src')->in(__DIR__ . '/tests')
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'blankline_after_open_tag',
        'double_arrow_multiline_whitespaces',
        'duplicate_semicolon',
        'empty_return',
        'extra_empty_lines',
        'function_typehint_space',
        'include',
        'join_function',
        'list_commas',
        'multiline_array_trailing_comma',
        'namespace_no_leading_whitespace',
        'new_with_braces',
        'no_blank_lines_after_class_opening',
        'object_operator',
        'operators_spaces',
        'remove_leading_slash_use',
        'remove_lines_between_uses',
        'return',
        'self_accessor',
        'single_array_no_trailing_comma',
        'single_blank_line_before_namespace',
        'spaces_before_semicolon',
        'spaces_cast',
        'standardize_not_equal',
        'ternary_spaces',
        'trim_array_spaces',
        'unalign_double_arrow',
        'unalign_equals',
        'unused_use',
        'whitespacy_lines',
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'trailing_spaces',
        'encoding',
        'ordered_use',
        'short_array_syntax',
    ]);
