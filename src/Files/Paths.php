<?php

namespace bultonFr\Utils\Files;

use Exception;

class Paths
{
    const EXCEP_ABS_REL_SAME_PATH = 203001;
    const EXCEP_ABS_REL_NOT_COMMON = 203002;

    /**
     * Resolve the relative path for two abslute path
     *
     * @param string $srcPath
     * @param string $destPath
     *
     * @return string
     */
    public static function absoluteToRelative(
        string $srcPath,
        string $destPath
    ): string {
        if ($srcPath === $destPath) {
            throw new Exception(
                'source path and dest path are same.',
                static::EXCEP_ABS_REL_SAME_PATH
            );
        }

        //Remove first slash to not have a empty string for key 0
        //If path not start with slash, it's not an absolute path !
        $srcPathEx  = explode('/', substr($srcPath, 1));
        $destPathEx = explode('/', substr(dirname($destPath), 1));

        $samePathStatus = true;
        $notSameIdx     = 0;
        $relativePath   = '';
        $endSrcPath     = '';

        foreach ($srcPathEx as $srcIdx => $srcItem) {
            if ($samePathStatus === true) {
                //Always in the same path
                if (
                    isset($destPathEx[$srcIdx])
                    && $srcItem === $destPathEx[$srcIdx]
                ) {
                    continue;
                }

                $samePathStatus = false;
                $notSameIdx     = $srcIdx;
            }

            //Not the same path, so we add srcItem to the var which contain
            //end of relative path which will be returned.
            $endSrcPath .= (empty($endSrcPath)) ? '' : '/';
            $endSrcPath .= $srcItem;
        }

        //First item of paths is not same, no common between path.
        if ($notSameIdx === 0) {
            throw new Exception(
                'source path and dest path have no common point at the beginning.',
                static::EXCEP_ABS_REL_NOT_COMMON
            );
        }

        $nbDestItem = count($destPathEx) - 1;
        for ($destIdx = $notSameIdx; $destIdx <= $nbDestItem; $destIdx++) {
            $relativePath .= '../';
        }

        $relativePath .= $endSrcPath;

        return $relativePath;
    }
}
