# Quality Tools Setup

This repository has been configured to support modern PHP quality tools. Due to network constraints during the initial setup, the tools are not pre-installed but can be easily added.

## GitHub Actions

The repository now uses GitHub Actions instead of Travis CI:
- ✅ Tests on PHP 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3
- ✅ Composer validation
- ✅ Automated dependency caching
- ✅ Quality tools integration ready

## Available Quality Tools

To add modern PHP quality tools, run these commands:

### PHPStan (Static Analysis)
```bash
composer require --dev phpstan/phpstan:^1.10
./vendor/bin/phpstan analyse --memory-limit=1G
```

### PHP CS Fixer (Code Formatting)
```bash
composer require --dev friendsofphp/php-cs-fixer:^3.0
./vendor/bin/php-cs-fixer fix --dry-run --diff --verbose
```

### PHPMD (Mess Detector)
```bash
composer require --dev phpmd/phpmd:^2.13
./vendor/bin/phpmd src text cleancode,codesize,controversial,design,naming,unusedcode
```

## Configuration Files

Pre-configured settings are available:
- `phpstan.neon` - PHPStan configuration (level 5, excludes tests)
- `.php-cs-fixer.php` - PHP CS Fixer configuration (PSR-12 + additional rules)

## Composer Scripts

Use these convenient scripts:
```bash
composer test          # Run atoum tests
composer check         # Validate composer.json
composer quality       # Run all quality checks
```

## Enabling Quality Tools in GitHub Actions

Once the tools are installed, uncomment the quality check steps in `.github/workflows/ci.yml`.

## Badges

The README.md has been updated with:
- ✅ GitHub Actions CI badge
- ✅ Packagist version badges  
- ✅ License badge
- ✅ PHP version requirement badge

Additional badges can be added once quality tools are active.