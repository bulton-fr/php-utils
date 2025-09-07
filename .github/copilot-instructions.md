# PHP Utils Library

PHP Utils is a utility library providing CLI and file management tools for PHP applications. It includes modules for colored terminal output (Cli\BasicMsg) and file system operations (Files\FileManager, Files\Paths, Files\ReadDirectory).

Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.

## Quick Start Workflow

For immediate development setup:
```bash
# 1. Install basic dependencies (fast, no external dependencies)
composer install --no-dev --ignore-platform-reqs

# 2. Validate everything works
php .github/copilot/validate.php

# 3. Before making changes, always run
composer validate

# 4. For full test suite (streamlined after PR #6):
# composer install --ignore-platform-reqs
```

## Working Effectively

### Requirements and Setup
- PHP 7.2+ required (composer.json constraint), supports PHP 7.2+ through 8.x using modern compatibility
- Composer for dependency management (v2+ optimized configuration)
- atoum testing framework for unit tests

### Bootstrap and Build Process
- `composer install --no-dev --ignore-platform-reqs` -- Install production dependencies only (~5 seconds)
- `COMPOSER_AUTH='{"github-oauth": {"github.com": "'$GH_COMPOSER_TOKEN'"}}' composer install --ignore-platform-reqs` -- Install all dependencies including dev tools using GitHub OAuth token. Set timeout to 300+ seconds.
- Autoloader available at `vendor/autoload.php` after install

### Security Requirements
- **NEVER commit GitHub tokens or secrets in clear text** - this is a critical security vulnerability
- Use environment variables or repository secrets for authentication
- Available token: `$GH_COMPOSER_TOKEN` environment variable for Composer GitHub API authentication

### Running Tests
- Full test suite uses atoum framework with simplified configuration (coverage disabled after PR #6)
- `./vendor/bin/atoum -c .atoum.php +verbose` -- Official test command. Set timeout to 120+ seconds.
- Manual functionality testing via custom test script works reliably (see Validation section)

### Build Timing Expectations
- Basic dependency install: 5-10 seconds
- Full dependency install with GitHub token: 120-300 seconds (44 packages including quality tools)
- Manual functionality tests: <1 second
- Composer validation: <5 seconds

## Validation

### Manual Testing (Recommended)
Always validate changes with the provided test script (`.github/copilot/validate.php`):

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

Run with: `php .github/copilot/validate.php` (takes <1 second)

### Extended Validation (Complete Feature Test)
For comprehensive testing, use the provided extended validation script (`.github/copilot/extended_validate.php`):

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
├── .atoum.php              # Test configuration (coverage disabled after PR #6)
├── .github/workflows/ci.yml # GitHub Actions CI configuration (PHP 7.2-8.3 after PR #10)
├── .php-cs-fixer.php       # PHP CS Fixer configuration (PSR-12 standards)
├── phpstan.neon            # PHPStan static analysis configuration (level 5)
├── composer.json           # Dependencies and autoload config (includes quality tools after PR #10)
├── QUALITY_TOOLS.md        # Documentation for quality tools usage
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
- Library targets PHP 7.2+ through 8.x (updated in PR #6 for broader modern compatibility)
- Use `--ignore-platform-reqs` flag with composer only if needed for edge cases
- CI environment (Travis) tests PHP 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3

### Documentation Locations
- Main library docs: `src/*/docs/README.md`
- Class-specific docs: `src/*/docs/[ClassName].md`
- All documentation includes method signatures and usage examples

## Troubleshooting

### Common Issues
- "Platform requirements" error: Use `composer install --ignore-platform-reqs` with GitHub token authentication
- GitHub API rate limits: Use `COMPOSER_AUTH='{"github-oauth": {"github.com": "'$GH_COMPOSER_TOKEN'"}}' composer install --ignore-platform-reqs`
- Missing vendor directory: Run basic `composer install --no-dev --ignore-platform-reqs` first
- Test failures: Verify PHP version compatibility and use manual validation scripts

### Quality Tools Available (PR #10)
- `composer analyze` - Run PHPStan static analysis
- `composer cs-check` - Check code style compliance (PSR-12)
- `composer cs-fix` - Fix code style issues automatically
- `composer phpmd` - Run PHP Mess Detector
- `composer quality` - Run all quality checks

### CI Integration
- GitHub Actions configured for PHP 7.2-8.3 (migrated in PR #10)
- Tests run via atoum with simplified configuration (coverage disabled after PR #6)
- Quality tools integrated in CI workflow with GitHub token authentication
- Composer validation required for successful CI builds

## NEVER CANCEL Operations
- `COMPOSER_AUTH='{"github-oauth": {"github.com": "'$GH_COMPOSER_TOKEN'"}}' composer install --ignore-platform-reqs` -- Set timeout to 300+ seconds (includes quality tools)
- `./vendor/bin/atoum` test runs -- Set timeout to 120+ seconds