<?php
namespace ByDm\MySQLMigrateData;

use Doctrine\DBAL\Connection;
use ByDm\MySQLMigrateData\Configuration\Configuration;

/**
 * Data Migrator
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class DataMigrator
{
    /**
     * @var Connection dbal connection
     */
    private $conn;
    
    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private $schemaManager;
    
    /**
     * Constructor
     * 
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
        $this->schemaManager = $this->conn->getSchemaManager();
    }
    
    /**
     * Migration configuration
     * 
     * @param \ByDm\MySQLMigrateData\Configuration\Configuration $config
     */
    public function generateQueries(Configuration $config)
    {
        $sourceDb      = $config->getSourceDb();
        $destinationDb = $config->getDestinationDb();
        
        $queries = array();
        foreach($config->getMapping() as $mapping) {
            $queries[] = $this->buildSql($sourceDb, $destinationDb, $mapping);
        }
        
        return $queries;
    }
    
    
    /**
     * Builds insert sql query by mapping
     * 
     * @param string $sourceDb source database name
     * @param string $destinationDb destination database name
     * @param array $mapping mapping of tables and columns
     * @return string
     */
    private function buildSql($sourceDb, $destinationDb, array $mapping)
    {
        $sql = "INSERT INTO %s (%s) SELECT %s FROM %s";
        $destinationColumns = array();
        $sourceColumns      = "";
        
        
        $destinationTable = $destinationDb . '.' .$mapping['destination'];
        $sourceTable      = $sourceDb . '.' . $mapping['source'] . ' AS s';
        
        //destination table doctrine column mapping
        $columns = $this->schemaManager->listTableColumns(
            $mapping['destination'], 
            $destinationDb
        );
        
        //destination column doctrine types colName => colType
        $columnTypes = array();
        foreach($columns as $column) {
            $columnTypes[$column->getName()] = $column->getType()->getName();
        }
        
        foreach($mapping['columns'] as $destinationColumn => $sourceDefenition) {
            $destinationColumns[] = '`' . trim($destinationColumn, '`') . '`';
            
            switch ($sourceDefenition['value_type']) {
                case Configuration::VALUE_TYPE_SCALAR:
                    $value = $sourceDefenition['value'];
                    if (null !== $value) {
                        $value = $this->conn->convertToDatabaseValue(
                            $value, 
                            $columnTypes[$destinationColumn]
                        );
                        if (is_string($value)) {
                            $value = "'" . $value . "'";
                        }
                    } else {
                        $value = "NULL";
                    }
                    
                    $sourceColumns .= $value . ', ';
                    break;
                case Configuration::VALUE_TYPE_TIMESTAMP:
                    $sourceColumns .= "'" . $this->conn->convertToDatabaseValue(
                        new \DateTime(), 
                        $columnTypes[$destinationColumn]
                    ) . "', ";
                    break;
                case Configuration::VALUE_TYPE_EXPRESSION:
                    $sourceColumns .= $sourceDefenition['value'] . ', ';
                default:
                    $sourceColumns .= 's.`' . trim($sourceDefenition['value'], '`') . '`, ';
                    break;
            }
        }
        
        $where = '';
        if (isset($mapping['condition'])) {
            $where .= 'WHERE ' . $mapping['condition'];
        }
        
        return sprintf(
            $sql, 
            $destinationTable, 
            implode(', ', $destinationColumns), 
            trim($sourceColumns, ', ') . $where,
            $sourceTable
        );
    }
}
