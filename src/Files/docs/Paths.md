# Utils\Files\Paths

This class give you many methods to play with paths.

## Methods

__`string public static function absoluteToRelative(string $srcPath, string $destPath)`__

Return the relative path for two absolute paths (`$srcPath`, `$destPath`).  
The name of argument is because this method has been created for find relative path for symlink items.
