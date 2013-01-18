<?php
namespace ByDm\MySQLMigrateData\Tests\Loader;

use ByDm\MySQLMigrateData\Loader\XmlLoader;

/**
 * Tests for xml loader
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * @var XmlLoader xml loader instance
     */
    private $xmlLoader;
    
    public function setUp()
    {
        $this->xmlLoader = new XmlLoader();
    }
    
    /**
     * @expectedException \ByDm\MySQLMigrateData\Exception\FileNotFoundException
     */
    public function testFileNotFoundException()
    {
        $this->xmlLoader->load("NOT_FOUND.sdf");
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNotValidXmlException()
    {
        $this->xmlLoader->load(__DIR__ . DIRECTORY_SEPARATOR . 'invalid_config.xml');
    }
    
    public function testLoad()
    {
        $config = $this->xmlLoader->load(__DIR__ . DIRECTORY_SEPARATOR . 'valid_config.xml');
        $this->assertEquals('db1', $config->getSourceDb());
        $this->assertEquals('db2', $config->getDestinationDb());
        
        $mapping = $config->getMapping();
        
        $this->assertEquals(array(
            array(
                'source'      => 'tab1',
                'destination' => 'tab2',
                'columns' => array(
                    'col1' => array(
                        'value_type' => 'timestamp'
                    ),
                    'col2' => array(
                        'value_type' => 'scalar',
                        'value'      => 3.11
                    ),
                )
            ),
            array(
                'source'      => 'tab3',
                'destination' => 'tab4',
                'columns' => array(
                    'col3' => array(
                        'value_type' => 'scalar',
                        'value'      => true
                    ),
                    'col4' => array(
                        'value_type' => 'scalar',
                        'value'      => null,
                    ),
                    'col5' => array(
                        'value_type' => 'column',
                        'value' => 'column_name'
                    )
                )
            ),
        ), $mapping);
        
    }
    
    public function tiredDown()
    {
        $this->xmlLoader = false;
    }
}
