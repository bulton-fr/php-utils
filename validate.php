<?php
require 'vendor/autoload.php';

use bultonFr\Utils\Cli\BasicMsg;
use bultonFr\Utils\Files\FileManager;
use bultonFr\Utils\Files\Paths;

echo "=== PHP Utils Validation Script ===\n";

// Test CLI colored output
echo "\nTesting CLI BasicMsg:\n";
BasicMsg::displayMsgNL("Success message", "green", "bold");
BasicMsg::displayMsgNL("Warning message", "yellow");
BasicMsg::displayMsgNL("Error message", "red");
BasicMsg::displayMsgNL("Normal message");

// Test file management
echo "\nTesting FileManager:\n";
$fileManager = new FileManager();
echo "FileManager instantiated successfully\n";

// Test path utilities
echo "\nTesting Paths:\n";
$relativePath = Paths::absoluteToRelative("/tmp/src", "/tmp/dest/file.txt");
echo "Relative path calculation: $relativePath\n";

echo "\n=== All validations passed! ===\n";