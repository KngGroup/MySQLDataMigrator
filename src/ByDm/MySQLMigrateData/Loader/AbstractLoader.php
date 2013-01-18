<?php
namespace ByDm\MySQLMigrateData\Loader;

use ByDm\MySQLMigrateData\Exception\FileNotFoundException;
use ByDm\MySQLMigrateData\Exception\FileNotReadableException;

/**
 * AbstractLoader class
 * 
 * implements locate function
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * Check if file exist and is readable
     */
    protected function checkFile($filename)
    {
        if (!file_exists($filename)) {
            throw new FileNotFoundException($filename);
        }
        
        if (!is_readable($filename)) {
            throw new FileNotReadableException($filename);
        }
    }
    
}
