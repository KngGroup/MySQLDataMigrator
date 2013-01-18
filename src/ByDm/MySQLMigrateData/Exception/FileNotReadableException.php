<?php
namespace ByDm\MySQLMigrateData\Exception;

/**
 * FileNotReadableException
 * 
 * throws if file is not readable
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class FileNotReadableException extends \InvalidArgumentException
{
    /**
     * {@inheritDoc}
     */
    public function __construct($filename, $code = null, $previous = null)
    {
        $message = $filename . ' is not readable';
        parent::__construct($message, $code, $previous);
    }
}
