<?php
$header = <<<'EOF'
This file is part of the 2amigos/yii2-usuario project.

(c) 2amigOS! <http://2amigos.us/>

For the full copyright and license information, please view
the LICENSE file that was distributed with this source code.
EOF;


$finder = PhpCsFixer\Finder::create()
    ->exclude(['resources'])
    ->in("src/User")
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'header_comment' => ['header' => $header],
        'combine_consecutive_unsets' => true,
        'no_extra_blank_lines' => [
          'tokens' => ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait',],
        ],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'phpdoc_align' => true,
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_whitespace_before_comma_in_array' => true,
        'trim_array_spaces' => true,
        'explicit_string_variable' => true,
        'binary_operator_spaces' => true,
    ])
    ->setFinder($finder)
;
