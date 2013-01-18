<?php
namespace ByDm\MySQLMigrateData\Tests;

use ByDm\MySQLMigrateData\Util\ToPhpValueConverter;

/**
 * Test for string to php valu converter
 */
class ToPhpValueConverterTest extends \PHPUnit_Framework_TestCase {
     
    public function testDataProvider()
    {
         return array(
             array('null', null),
             array('false', false),
             array('true', true),
             array('0xFF', 255),
             array('0', 0),
             array('011', 9),
             array('0xFF', 255),
             array('100', 100),
             array('str', 'str'),
         );
    }
    
    /**
     * @dataProvider testDataProvider
     */
    public function testConvert($given, $expected)
    {
        $this->assertEquals($expected, ToPhpValueConverter::convert($given));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidArgumentException()
    {
        ToPhpValueConverter::convert(new \DateTime());
    }
    
    public function floatDataProvider()
    {
        return array(
            array('0.11', 0.11),
            array('15.11', 15.11)
        );
    }
    
    /**
     * @dataProvider floatDataProvider
     */
    public function testFloatValues($given, $expected)
    {
        $actual = ToPhpValueConverter::convert($given);
        $this->assertLessThan(0.01, abs($expected - $actual));
    }
}
