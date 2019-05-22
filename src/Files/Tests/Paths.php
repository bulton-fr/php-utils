<?php

namespace bultonFr\Utils\Files\Tests\units;

use atoum;
use bultonFr\Utils\Files\Paths as TestedPaths;

/**
 * @engine isolate
 */
class Paths extends atoum
{
    public function testAbsoluteToRelative()
    {
        $this->assert('test Files\FileManager::absoluteToRelative - same path')
            ->exception(function () {
                TestedPaths::absoluteToRelative(
                    '/var/www/myWebsite/v1.0/vendor/bulton-fr/bfw-sql/src',
                    '/var/www/myWebsite/v1.0/vendor/bulton-fr/bfw-sql/src'
                );
            })
                ->hasCode(TestedPaths::EXCEP_ABS_REL_SAME_PATH)
        ;

        $this->assert('test Files\FileManager::absoluteToRelative - no common path')
            ->exception(function () {
                TestedPaths::absoluteToRelative(
                    '/var/www/myWebsite/v1.0/vendor/bulton-fr/bfw-sql/src',
                    '/home/myWebsite/v1.0/app/modules/bfw-sql'
                );
            })
                ->hasCode(TestedPaths::EXCEP_ABS_REL_NOT_COMMON)
        ;

        $this->assert('test Files\FileManager::absoluteToRelative - common path')
            ->string(TestedPaths::absoluteToRelative(
                '/var/www/myWebsite/v1.0/vendor/bulton-fr/bfw-sql/src',
                '/var/www/myWebsite/v1.0/app/modules/bfw-sql'
            ))
                ->isEqualTo('../../vendor/bulton-fr/bfw-sql/src')
        ;
    }
}
