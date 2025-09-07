<?php

require __DIR__ . '/vendor/autoload.php';

use atoum\atoum\reports\asynchronous\clover;
use atoum\atoum\writers\file as FileWriter;

$script->addDefaultReport();

// Re-enable code coverage for modern CI
$script->noCodeCoverageForClasses('\\atoum\\atoum\\*');
$script->excludeDirectoriesFromCoverage([__DIR__ . '/vendor']);

// Add XML coverage report for Codecov
if (getenv('CI') === 'true') {
    $coverageReportPath = __DIR__ . '/coverage.xml';
    $cloverReport = new clover();
    $fileWriter = new FileWriter($coverageReportPath);
    $cloverReport->addWriter($fileWriter);
    $script->addReport($cloverReport);
}

$runner->addTestsFromDirectory(__DIR__.'/src/Cli/Tests');
$runner->addTestsFromDirectory(__DIR__.'/src/Files/Tests');
