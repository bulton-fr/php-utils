<?php

require __DIR__ . '/vendor/autoload.php';

use atoum\atoum;

$script->addDefaultReport();

$runner->addTestsFromDirectory(__DIR__.'/src/Cli/Tests');
$runner->addTestsFromDirectory(__DIR__.'/src/Files/Tests');

$cloverWriter = new atoum\writers\file('./clover.xml');
$cloverReport = new atoum\reports\asynchronous\clover;
$cloverReport->addWriter($cloverWriter);

$runner->addReport($cloverReport);
