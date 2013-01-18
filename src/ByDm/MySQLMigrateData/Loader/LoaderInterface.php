<?php
namespace ByDm\MySQLMigrateData\Loader;

/**
 * Data loader interface
 * 
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
interface LoaderInterface
{
    /**
     * Loads migration configuration from file
     * 
     * @param string $filename
     * @return \ByDm\MySQLMigrateData\Configuration\Configuration
     */
    public function load($filename);
}
