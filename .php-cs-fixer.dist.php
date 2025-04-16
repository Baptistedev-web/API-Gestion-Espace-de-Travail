<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'], // Utilisation des tableaux courts []
        'binary_operator_spaces' => ['default' => 'align_single_space'], // Alignement des opérateurs binaires
        'blank_line_after_namespace' => true, // Ligne vide après le namespace
        'blank_line_after_opening_tag' => true, // Ligne vide après l'ouverture PHP
        'concat_space' => ['spacing' => 'one'], // Espace autour des concaténations
        'declare_strict_types' => true, // Ajout de `declare(strict_types=1);`
        'no_unused_imports' => true, // Suppression des imports inutilisés
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Tri des imports par ordre alphabétique
        'phpdoc_align' => ['align' => 'vertical'], // Alignement des annotations PHPDoc
        'phpdoc_order' => true, // Ordre des annotations PHPDoc
        'single_quote' => true, // Utilisation des guillemets simples
        'trailing_comma_in_multiline' => ['elements' => ['arrays']], // Virgule finale dans les tableaux multilignes
    ])
    ->setRiskyAllowed(true) // Autoriser les fixers risquées
    ->setFinder($finder)
;
