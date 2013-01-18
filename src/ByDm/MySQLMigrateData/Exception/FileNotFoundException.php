<?php
namespace ByDm\MySQLMigrateData\Exception;

/**
 * FileNotFoundException
 * 
 * throws if file does not exist
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class FileNotFoundException extends \InvalidArgumentException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($filename, $code = null, $previous = null)
    {
        $message = $filename . ' was not found';
        parent::__construct($message, $code, $previous);
    }
}
