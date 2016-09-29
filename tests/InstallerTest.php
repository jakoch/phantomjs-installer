<?php

namespace PhantomInstaller\Test;

use Composer\Config;
use PhantomInstaller\Installer;
use PhantomInstaller\PhantomBinary;

/**
 * @backupStaticAttributes enabled
 */
class InstallerTest extends \PHPUnit_Framework_TestCase
{
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
        $this->assertTrue(Installer::dropClassWithPathToInstalledBinary($binaryPath));
        $this->assertTrue(is_file(dirname(__DIR__) . '/src/PhantomInstaller/PhantomBinary.php'));

        // test the generated file
        require_once dirname(__DIR__) . '/src/PhantomInstaller/PhantomBinary.php';
        $this->assertSame($binaryPath, PhantomBinary::BIN);
        $this->assertSame(dirname($binaryPath), PhantomBinary::DIR);
    }

    public function testGetCdnUrl()
    {
        $version = '1.0.0';
        $cdnurl = Installer::getCdnUrl($version);
        $this->assertSame('https://bitbucket.org/ariya/phantomjs/downloads/', $cdnurl);

        $_ENV['PHANTOMJS_CDNURL'] = 'https://cnpmjs.org/downloads'; // without slash
        $cdnurl = Installer::getCdnUrl($version);
        $this->assertSame('https://cnpmjs.org/downloads/', $cdnurl);

        $_ENV['PHANTOMJS_CDNURL'] = 'https://github.com/medium/phantomjs';
        $cdnurl = Installer::getCdnUrl($version);
        $this->assertSame('https://github.com/medium/phantomjs/releases/download/v' . $version . '/', $cdnurl);

        unset($_ENV['PHANTOMJS_CDNURL']);
        $_SERVER['PHANTOMJS_CDNURL'] = 'scheme://another-download-url';
        $cdnurl = Installer::getCdnUrl($version);
        $this->assertSame('scheme://another-download-url/', $cdnurl);
    }

    public function testgetURL()
    {
        $version = '1.0.0';
        $url = Installer::getURL($version);
        $this->assertTrue(is_string($url));
    }

    public function testGetOS()
    {
        $os = Installer::getOS();
        $this->assertTrue(is_string($os));
    }

    public function testGetBitSize()
    {
        $bitsize = Installer::getBitSize();
        $this->assertTrue(is_string($bitsize));
    }
}
