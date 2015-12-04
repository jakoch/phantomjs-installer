<?php

namespace Tests;

include dirname(__DIR__) . '/src/PhantomInstaller/Installer.php';
#use \PhantomInstaller\Installer;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhantomInstaller\Installer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        //$this->object = new Installer;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    public function tearDown()
    {
    }

    public function testInstallPhantomJS()
    {
        // composer testing: mocks.. for nothing
        //InstallPhantomJS(Event $event)
        $this->assertTrue(true); // contribute ?
    }

    public function testCopyPhantomJsBinaryToBinFolder()
    {
        $this->assertTrue(true); // contribute ?
    }

    public function testDropClassWithPathToInstalledBinary()
    {
        $binaryPath = __DIR__ . '/a_fake_phantomjs_binary';

        // generate file
        $this->assertTrue(\PhantomInstaller\Installer::dropClassWithPathToInstalledBinary($binaryPath));
        $this->assertTrue(is_file(dirname(__DIR__).'/src/PhantomInstaller/PhantomBinary.php'));

        // test the generated file
        require_once dirname(__DIR__).'/src/PhantomInstaller/PhantomBinary.php';
        $this->assertSame($binaryPath,          \PhantomInstaller\PhantomBinary::BIN);
        $this->assertSame(dirname($binaryPath), \PhantomInstaller\PhantomBinary::DIR);
    }

    public function testgetURL()
    {
        $version = '1.0.0';
        $os = \PhantomInstaller\Installer::getURL($version);
        $this->assertTrue(is_string($os));
    }

    public function testGetOS()
    {
        $os = \PhantomInstaller\Installer::getOS();
        $this->assertTrue(is_string($os));
    }

    public function testGetBitSize()
    {
        $bitsize = \PhantomInstaller\Installer::getBitSize();
        $this->assertTrue(is_integer($bitsize));
    }
}
