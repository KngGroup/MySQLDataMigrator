<?php
namespace ByDm\MySQLMigrateData\Loader;

use ByDm\MySQLMigrateData\Configuration\Configuration;
use ByDm\MySQLMigrateData\Util\ToPhpValueConverter;

/**
 * Xml loader class
 * 
 * loads configuration from xml
 *
 * @author Dmitry Bykov <dmitry.bykov@sibers.com>
 */
class XmlLoader extends AbstractLoader
{
    
    /**
     * {@inheritDoc}
     */
    public function load($filename)
    {
        $this->checkFile($filename);
        
        //to prevent side effect, save old values
        $disableEntityLoader = libxml_disable_entity_loader(true);
        $useInternalErros    = libxml_use_internal_errors(true);
        
        $dom = new \DOMDocument();
        
        if (!$dom->loadXML(file_get_contents($filename))) {
            throw new \InvalidArgumentException(
                implode("\n", $this->getLibxmlErrors())
            );
        }
        
        $this->validate($dom);
        $dom->normalize();
        
        $configuration = $this->buildConfiguration($dom);
        libxml_disable_entity_loader($disableEntityLoader);
        libxml_use_internal_errors($useInternalErros);
        
        return $configuration;
    }
    
    /**
     * Validates dom document by schema
     * 
     * @param \DOMDocument $dom
     * @throws \InvalidArgumentException if schema is invalid
     */
    private function validate(\DOMDocument $dom)
    {
        $schemaFilename = __DIR__ . DIRECTORY_SEPARATOR . '..' 
                          . DIRECTORY_SEPARATOR . 'Resources' 
                          . DIRECTORY_SEPARATOR . 'migration_configuration.xsd';
        
        if (!$dom->schemaValidateSource(file_get_contents($schemaFilename))) {
            throw new \InvalidArgumentException(
                implode("\n", $this->getLibxmlErrors())
            );
        }
    }
    
    /**
     * Returns current libxml errors
     * 
     * @return array
     */
    private function getLibxmlErrors()
    {
        $errors = array();
        
        foreach(libxml_get_errors() as $error) {
            $errors[] = sprintf(
                    "%s: %s in %s on line %d:%d",
                    (LIBXML_ERR_WARNING == $error->level) ? 'Warining' : 'Error',
                    $error->message,
                    $error->file,
                    $error->line,
                    $error->column
            );
            
        }
        
        libxml_clear_errors();
        return $errors;
    }
    
    /**
     * Returns migration configuration
     * 
     * @param \DOMDocument $dom
     * @return \ByDm\MySQLMigrateData\Configuration\Configuration
     */
    private function buildConfiguration(\DOMDocument $dom)
    {
        $xml = simplexml_import_dom($dom);
        $configuration = new Configuration();
        
        $sourceDb = (string) $xml['source'];
        $configuration->setSourceDb($sourceDb);
        
        $destinationDb = (string) $xml['destination'];
        $configuration->setDestinationDb($destinationDb);
        
        $mapping = array();
        foreach($xml->xpath('/migration/tables/table') as $tableNode) {
            $tableMapping = array(
                'source'      => (string) $tableNode['source'],
                'destination' => (string) $tableNode['destination'],
                'columns'      => array()
            );
            
            foreach($tableNode->columns->children() as $columnNode) {
                $valueType = (string) $columnNode['value_type'];
                if (!$valueType) {
                    $valueType = 'column';
                }
                $columnMapping['value_type'] = $valueType;
                
                if (!isset($columnNode['value'])) {
                    if ($columnMapping['value_type'] != Configuration::VALUE_TYPE_TIMESTAMP) {
                        throw new \InvalidArgumentException(
                            'Value must be defined for ' 
                            . $columnMapping['destination'] . 'column'
                        );
                    }
                } else {
                    $value = (string) $columnNode['value'];
                    if ($columnMapping['value_type'] == Configuration::VALUE_TYPE_SCALAR) {
                        $value = ToPhpValueConverter::convert($value);
                    }
                    $columnMapping['value'] = $value;
                }
                $tableMapping['columns'][(string) $columnNode['destination']] = $columnMapping;
            }
            $mapping[] = $tableMapping;
        }
        
        $configuration->setMapping($mapping);
        return $configuration;
    }
}
