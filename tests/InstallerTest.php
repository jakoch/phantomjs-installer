<?php

namespace PhantomInstaller\Test;

use PhantomInstaller\Installer;
use PhantomInstaller\PhantomBinary;

/**
 * @backupStaticAttributes enabled
 */
class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Installer */
    protected $object;
    
    protected function setUp()
    {
        parent::setUp();

        $mockComposer = $this->getMockComposer();
        $mockIO = $this->getMockIO();
        $this->object = new Installer($mockComposer, $mockIO);
    }

    protected function getMockComposer()
    {
        $mockComposer = $this->getMockBuilder('Composer\Composer')->getMock();

        return $mockComposer;
    }

    protected function getMockIO()
    {
        $mockIO = $this->getMockBuilder('Composer\IO\BaseIO')->getMock();

        return $mockIO;
    }

    public function testInstallPhantomJS()
    {
        // composer testing: mocks.. for nothing
        //InstallPhantomJS(Event $event)
        $this->markTestSkipped('contribute ?');
    }

    public function testCopyPhantomJsBinaryToBinFolder()
    {
        $this->markTestSkipped('contribute ?');
    }

    public function testDropClassWithPathToInstalledBinary()
    {
        $binaryPath = __DIR__ . '/a_fake_phantomjs_binary';

        // generate file
        $this->assertTrue($this->object->dropClassWithPathToInstalledBinary($binaryPath));
        $this->assertTrue(is_file(dirname(__DIR__) . '/src/PhantomInstaller/PhantomBinary.php'));

        // test the generated file
        require_once dirname(__DIR__) . '/src/PhantomInstaller/PhantomBinary.php';
        $this->assertSame($binaryPath, PhantomBinary::BIN);
        $this->assertSame(dirname($binaryPath), PhantomBinary::DIR);
    }

    public function testGetCdnUrl()
    {
        $version = '1.0.0';
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame('https://bitbucket.org/ariya/phantomjs/downloads/', $cdnurl);

        $_ENV['PHANTOMJS_CDNURL'] = 'https://cnpmjs.org/downloads'; // without slash
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame('https://cnpmjs.org/downloads/', $cdnurl);

        $_ENV['PHANTOMJS_CDNURL'] = 'https://github.com/medium/phantomjs';
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame('https://github.com/medium/phantomjs/releases/download/v' . $version . '/', $cdnurl);

        unset($_ENV['PHANTOMJS_CDNURL']);
        $_SERVER['PHANTOMJS_CDNURL'] = 'scheme://another-download-url';
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame('scheme://another-download-url/', $cdnurl);
    }

    public function testgetURL()
    {
        $version = '1.0.0';
        $url = $this->object->getURL($version);
        $this->assertTrue(is_string($url));
    }

    public function testGetOS()
    {
        $os = $this->object->getOS();
        $this->assertTrue(is_string($os));
    }

    public function testGetBitSize()
    {
        $bitsize = $this->object->getBitSize();
        $this->assertTrue(is_string($bitsize));
    }
}
