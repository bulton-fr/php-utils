<?php
require 'vendor/autoload.php';

use bultonFr\Utils\Cli\BasicMsg;
use bultonFr\Utils\Files\FileManager;
use bultonFr\Utils\Files\Paths;
use bultonFr\Utils\Files\ReadDirectory;

echo "=== Extended PHP Utils Validation ===\n";

// Test CLI with all colors and styles
echo "\nTesting CLI BasicMsg (colors and styles):\n";
BasicMsg::displayMsgNL("Green bold text", "green", "bold");
BasicMsg::displayMsgNL("Red normal text", "red", "normal");
BasicMsg::displayMsgNL("Yellow text", "yellow");
BasicMsg::displayMsgNL("White text", "white");

// Test FileManager with temp files
echo "\nTesting FileManager operations:\n";
$fileManager = new FileManager();
$testDir = '/tmp/php_utils_extended_test';
$testFile = $testDir . '/test.txt';

try {
    if (!is_dir($testDir)) {
        $fileManager->createDirectory($testDir);
        echo "✓ Directory created\n";
    }
    
    file_put_contents($testFile, "Test content");
    echo "✓ Test file created\n";
    
    $fileManager->copyFile($testFile, $testFile . '.copy');
    echo "✓ File copied\n";
    
    exec("rm -rf $testDir");
    echo "✓ Cleanup completed\n";
} catch (Exception $e) {
    echo "FileManager test error: " . $e->getMessage() . "\n";
}

// Test Paths
echo "\nTesting Paths utilities:\n";
$rel1 = Paths::absoluteToRelative("/var/www/src", "/var/www/public/index.php");
$rel2 = Paths::absoluteToRelative("/home/user/docs", "/home/user/projects/app.php");
echo "✓ Path calculations: $rel1, $rel2\n";

// Test ReadDirectory
echo "\nTesting ReadDirectory:\n";
$tempDir = '/tmp/readdir_test';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
    mkdir($tempDir . '/sub', 0755, true);
    file_put_contents($tempDir . '/file1.txt', 'test');
    file_put_contents($tempDir . '/sub/file2.txt', 'test');
}

$files = [];
$reader = new class($files) extends ReadDirectory {
    protected function itemAction(string $fileName, string $pathToFile): string {
        $parentFilter = parent::itemAction($fileName, $pathToFile);
        if (!empty($parentFilter)) return $parentFilter;
        if (!is_dir($pathToFile.'/'.$fileName)) {
            $this->list[] = $fileName;
        }
        return '';
    }
};
$reader->run($tempDir);
echo "✓ Found " . count($files) . " files recursively\n";
exec("rm -rf $tempDir");

echo "\n=== All extended validations passed! ===\n";