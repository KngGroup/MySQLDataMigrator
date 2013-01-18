<?php
namespace ByDm\MySQLMigrateData\Configuration;

/**
 * Migration Configuration
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class Configuration
{
    /**
     * Scalar value (eg strings, integers, floats)
     */
    const VALUE_TYPE_SCALAR     = 'scalar';
    
    /**
     * If column is Date or Datetime then CURRENT_DATE() will be used
     * otherwise current timestamp
     */
    const VALUE_TYPE_TIMESTAMP  = 'timestamp';
    
    /**
     * Value from column
     */
    const VALUE_TYPE_COLUMN     = 'column';
    
    /**
     * Expression to calculate value, prefix for columns 's.'
     */
    const VALUE_TYPE_EXPRESSION = 'expression';
    
    /**
     * @var string source database
     */
    private $sourceDb;
    
    /**
     * @var string destination database
     */
    private $destinationDb;
    
    /**
     * @var array mapping tables
     */
    private $mapping;
    
    /**
     * Returns source database name
     * 
     * @return string
     */
    public function getSourceDb()
    {
        return $this->sourceDb;
    }
    
    /**
     * Sets source database
     * 
     * @param string $sourceDb
     */
    public function setSourceDb($sourceDb)
    {
        $this->sourceDb = $sourceDb;
        
        return $this;
    }
    
    /**
     * Returns destination database name
     * 
     * @return string
     */
    public function getDestinationDb()
    {
        return $this->destinationDb;
    }
    
    /**
     * Sets destination database name
     * 
     * @param string $toDb
     */
    public function setDestinationDb($toDb)
    {
        $this->destinationDb = $toDb;
        
        return $this;
    }
    
    /**
     * Returns columns mapping information
     * 
     * @return array destination_column => source
     */
    public function getMapping()
    {
        return $this->mapping;
    }
    
    /**
     * Sets mapping information
     * 
     * @param array $mapping destination_column => source
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
        
        return $this;
    }

}
