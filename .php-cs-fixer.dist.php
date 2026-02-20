<?php

$finder = new PhpCsFixer\Finder()->in([__DIR__ . "/src", __DIR__ . "/tests"]);

return new PhpCsFixer\Config()
    ->setRules([
        "@PER-CS" => true,
        "@PHP82Migration" => true,
        "class_attributes_separation" => [
            "elements" => [
                "const" => "one",
                "method" => "one",
                "property" => "one",
                "trait_import" => "none",
            ],
        ],
        "no_extra_blank_lines" => [
            "tokens" => ["extra", "throw", "use"],
        ],
        "no_blank_lines_after_class_opening" => true,
        "no_blank_lines_after_phpdoc" => true,
        "no_closing_tag" => true,
        "no_empty_phpdoc" => true,
        "no_empty_statement" => true,

        //'strict_param' => true,
        "array_indentation" => true,
        "array_syntax" => ["syntax" => "short"],
        "binary_operator_spaces" => [
            "default" => "single_space",
            "operators" => ["=>" => null],
        ],
        "whitespace_after_comma_in_array" => true,
    ])
    ->setFinder($finder);
