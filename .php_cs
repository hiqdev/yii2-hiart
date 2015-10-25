<?php

$header = <<<EOF
Tools to use API as ActiveRecord for Yii2

@link      https://github.com/hiqdev/yii2-hiart
@package   yii2-hiart
@license   BSD-3-Clause
@copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
EOF;

Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-long_array_syntax',                    /// Arrays should use the long syntax.
        '-php4_constructor',                     /// Convert PHP4-style constructors to __construct. Warning! This could change code behavior.
        '-phpdoc_var_to_type',                   /// @var should always be written as @type.
        'header_comment',                        /// Add, replace or remove header comment.
        'align_double_arrow',                    /// Align double arrow symbols in consecutive lines.
        'align_equals',                          /// Align equals symbols in consecutive lines.
        'concat_with_spaces',                    /// Concatenation should be used with at least one whitespace around.
        'ereg_to_preg',                          /// Replace deprecated ereg regular expression functions with preg. Warning! This could change code behavior.
        'multiline_spaces_before_semicolon',     /// Multi-line whitespace before closing semicolon are prohibited.
        'newline_after_open_tag',                /// Ensure there is no code on the same line as the PHP open tag.
        'single_blank_line_before_namespace',    /// There should be no blank lines before a namespace declaration.
        'ordered_use',                           /// Ordering use statements.
        'phpdoc_order',                          /// Annotations in phpdocs should be ordered so that @param come first, then @throws, then @return.
        'pre_increment',                         /// Pre incrementation/decrementation should be used if possible.
        'short_array_syntax',                    /// PHP arrays should use the PHP 5.4 short-syntax.
        'strict',                                /// Comparison should be strict. Warning! This could change code behavior.
        'strict_param',                          /// Functions should be used with $strict param. Warning! This could change code behavior.
    ])
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in(__DIR__)
            ->notPath('vendor')
            ->notPath('runtime')
            ->notPath('web/assets')
            ->notPath('tests/unit/UnitTester.php')
    )
;
