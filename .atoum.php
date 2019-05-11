<?php

require __DIR__ . '/vendor/autoload.php';

$runner->addTestsFromDirectory(__DIR__.'/src/Cli/Tests');
$runner->addTestsFromDirectory(__DIR__.'/src/Files/Tests');
