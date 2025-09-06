# PHP Utils Library

PHP Utils is a utility library providing CLI and file management tools for PHP applications. It includes modules for colored terminal output (Cli\BasicMsg) and file system operations (Files\FileManager, Files\Paths, Files\ReadDirectory).

Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.

## Quick Start Workflow

For immediate development setup:
```bash
# 1. Install basic dependencies (fast, no GitHub token needed)
composer install --no-dev --ignore-platform-reqs

# 2. Validate everything works
php validate.php

# 3. Before making changes, always run
composer validate

# 4. For full test suite (optional, may prompt for GitHub token):
# composer install --ignore-platform-reqs
```

## Working Effectively

### Requirements and Setup
- PHP 7.1+ required (composer.json constraint), but works with PHP 8.x using platform requirement overrides
- Composer for dependency management
- atoum testing framework for unit tests

### Bootstrap and Build Process
- `composer install --no-dev --ignore-platform-reqs` -- Install production dependencies only (~5 seconds)
- `composer install --ignore-platform-reqs` -- Install all dependencies including dev tools. NEVER CANCEL: May require GitHub token for some packages, can take 2-5 minutes with network delays. Set timeout to 300+ seconds.
- Autoloader available at `vendor/autoload.php` after install

### Running Tests
- IMPORTANT: Full test suite requires atoum framework installation which has PHP version compatibility issues
- `./vendor/bin/atoum -c .atoum.php +verbose` -- Official test command. NEVER CANCEL: Can take 1-2 minutes. Set timeout to 180+ seconds.
- Manual functionality testing via custom test script works reliably (see Validation section)

### Build Timing Expectations
- Basic dependency install: 5-10 seconds
- Full dependency install with platform overrides: 120-300 seconds (NEVER CANCEL - GitHub API rate limits cause delays)
- Manual functionality tests: <1 second
- Composer validation: <5 seconds

## Validation

### Manual Testing (Recommended)
Always validate changes with this test script (save as `validate.php`):

```php
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
```

Run with: `php validate.php` (takes <1 second)

### Extended Validation (Complete Feature Test)
For comprehensive testing, use this extended validation script:

```php
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
```
### End-to-End Validation Scenarios
- ALWAYS test CLI color output in terminal after modifying BasicMsg class
- ALWAYS test file operations after modifying FileManager (create temp files, test copy/symlink operations)
- ALWAYS test path calculations after modifying Paths class
- ALWAYS test directory traversal after modifying ReadDirectory class
- ALWAYS run `composer validate` before committing changes

### Pre-commit Validation
- `composer validate` -- Validates composer.json structure (required for CI)
- Run manual test script to verify core functionality
- No automated linting configured - rely on PHP syntax validation

## Common Tasks

### Repo Structure
```
.
├── .atoum.php              # Test configuration
├── .travis.yml             # CI configuration (PHP 7.1-7.3)
├── composer.json           # Dependencies and autoload config
├── src/
│   ├── Cli/
│   │   ├── BasicMsg.php    # Colored terminal output utilities
│   │   ├── Tests/          # Unit tests for CLI module
│   │   └── docs/           # CLI documentation
│   ├── Files/
│   │   ├── FileManager.php # File and directory operations
│   │   ├── Paths.php       # Path manipulation utilities
│   │   ├── ReadDirectory.php # Recursive directory reading
│   │   ├── Tests/          # Unit tests for Files module
│   │   └── docs/           # Files documentation
│   └── Tests/
│       └── Helpers/        # Test helper classes
```

### Key Classes and Usage
- `bultonFr\Utils\Cli\BasicMsg`: Terminal output with colors (red, green, yellow, white) and styles (normal, bold)
- `bultonFr\Utils\Files\FileManager`: File operations (copy, symlink, directory management)
- `bultonFr\Utils\Files\Paths`: Path utilities (absolute to relative path conversion)
- `bultonFr\Utils\Files\ReadDirectory`: Recursive directory traversal with customizable actions

### Working with PHP Version Compatibility
- Original code targets PHP 7.1-7.3 but works with PHP 8.x
- Use `--ignore-platform-reqs` flag with composer when PHP version constraints fail
- CI environment (Travis) tests PHP 7.1, 7.2, 7.3 only

### Documentation Locations
- Main library docs: `src/*/docs/README.md`
- Class-specific docs: `src/*/docs/[ClassName].md`
- All documentation includes method signatures and usage examples

## Troubleshooting

### Common Issues
- "Platform requirements" error: Use `composer install --ignore-platform-reqs`
- GitHub rate limiting during install: Wait or provide GitHub token when prompted
- Missing vendor directory: Run basic `composer install --no-dev --ignore-platform-reqs` first
- Test failures: Verify PHP version compatibility and use manual validation scripts

### Included Validation Scripts
- `validate.php` - Basic functionality test (CLI, FileManager, Paths)
- `extended_validate.php` - Comprehensive test including ReadDirectory and file operations

### CI Integration
- Travis CI configured for PHP 7.1-7.3
- Tests run via atoum with coverage reporting to Scrutinizer
- Composer validation required for successful CI builds

## NEVER CANCEL Operations
- `composer install --ignore-platform-reqs` -- Set timeout to 300+ seconds
- `./vendor/bin/atoum` test runs -- Set timeout to 180+ seconds
- Any Composer operations that prompt for GitHub tokens -- Wait for input or provide token