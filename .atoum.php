<?php

require __DIR__ . '/vendor/autoload.php';

// Support both atoum 3.x (mageekguy\atoum) and 4.x (atoum\atoum) namespaces
if (class_exists('atoum\atoum\scripts\runner')) {
    // atoum 4.x
    use atoum\atoum;
} else {
    // atoum 3.x
    use mageekguy\atoum;
}

$script->addDefaultReport();

$runner->addTestsFromDirectory(__DIR__.'/src/Cli/Tests');
$runner->addTestsFromDirectory(__DIR__.'/src/Files/Tests');

$cloverWriter = new atoum\writers\file('./clover.xml');
$cloverReport = new atoum\reports\asynchronous\clover;
$cloverReport->addWriter($cloverWriter);

$runner->addReport($cloverReport);
