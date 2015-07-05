<?php
namespace SoliantTest\SimpleFM\Loader;

use Soliant\SimpleFM\HostConnection;
use Soliant\SimpleFM\Adapter;
use Soliant\SimpleFM\Loader\AbstractLoader;
use Soliant\SimpleFM\Loader\Mock as MockLoader;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-07-03 at 13:27:03.
 */
class AbstractLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::createCredentials
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::createArgs
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::createCommandURL
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::setAdapterCommandUrlDebug
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::prepare
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::getLastError
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::handleReturn
     * @covers Soliant\SimpleFM\Loader\AbstractLoader::errorCapture
     */
    public function testLoad()
    {
        $params=array('hostname'=>'localhost','dbname'=>'testdb','username'=>'Admin','password'=>'');
        $hostConnection = new HostConnection(
            $params['hostname'],
            $params['dbname'],
            $params['username'],
            $params['password']
        );
        $testXmlFile = file_get_contents(__DIR__ . '/../TestAssets/sample_fmresultset.xml');
        $loader = new MockLoader();
        $loader->setTestXml($testXmlFile);
        $adapter = new Adapter($hostConnection, $loader);
        $result = $loader->load($adapter);

        $this->assertArrayHasKey('errorCode', $loader->getLastError());
        $this->assertInstanceOf(AbstractLoader::class, $loader);
    }
}
