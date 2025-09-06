<?php

require __DIR__ . '/vendor/autoload.php';

// Disable code coverage since we're not using scrutinizer anymore
$runner->disableCodeCoverage();

$script->addDefaultReport();

$runner->addTestsFromDirectory(__DIR__.'/src/Cli/Tests');
$runner->addTestsFromDirectory(__DIR__.'/src/Files/Tests');
