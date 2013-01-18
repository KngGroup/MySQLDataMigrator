<?php
namespace ByDm\MySQLMigrateData\Util;

/**
 * Convert string to PHP value
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class ToPhpValueConverter
{
    /**
     * Converts string to php value
     * 
     * @param type $val
     */
    public static function convert($originalVal)
    {
        if (is_object($originalVal)) {
            throw new \InvalidArgumentException(
                'Value must be scalar or object given'
            );
        }
        
        $val = trim(strtolower($originalVal));
        
        switch(true) {
            case ctype_digit($val):
                return ($val[0] == '0') ? octdec($val) : (int) $val;
            case is_numeric($val):
                return ($val[1] == 'x') ? hexdec($val) : (float) $val;
            case $val == 'null':
                return null;
            case $val == 'true':
                return true;
            case $val == 'false':
                return false;
            default:
                return $originalVal;
        }
    }
}
