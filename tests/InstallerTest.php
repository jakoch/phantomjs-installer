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

    protected $bakEnvVars = array();

    protected $bakServerVars = array();
    
    protected function setUp()
    {
        parent::setUp();

        $mockComposer = $this->getMockComposer();
        $mockIO = $this->getMockIO();
        $this->object = new Installer($mockComposer, $mockIO);

        // Backup $_ENV and $_SERVER
        $this->bakEnvVars = $_ENV;
        $this->bakServerVars = $_SERVER;
    }

    protected function tearDown()
    {
        // Restore $_ENV and $_SERVER
        $_ENV = $this->bakEnvVars;
        $_SERVER = $this->bakServerVars;
    }

    protected function getMockComposer()
    {
        $mockComposer = $this->getMockBuilder('Composer\Composer')->getMock();

        return $mockComposer;
    }

    protected function getMockIO()
    {
        $mockIO = $this->getMockBuilder('Composer\IO\BaseIO')->getMockForAbstractClass();

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

    /**
     * @param array $extraConfig mock composer.json 'extra' config with this array
     */
    public function setUpForGetCdnUrl(array $extraConfig = array())
    {
        $object = $this->object;
        $mockComposer = $this->getMockComposer();
        $object->setComposer($mockComposer);
        $mockPackage = $this->getMockBuilder('Composer\Package\RootPackageInterface')->getMock();
        $mockComposer->method('getPackage')->willReturn($mockPackage);
        $mockPackage->method('getExtra')->willReturn($extraConfig);
    }

    /**
     * @backupGlobals
     */
    public function testCdnUrlTrailingSlash()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';
        $configuredCdnUrl = 'scheme://host/path'; // without slash
        $_ENV['PHANTOMJS_CDNURL'] = $configuredCdnUrl;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertRegExp('{(?:^|[^/])/$}', $cdnurl, 'CdnUrl should end with one slash.');
    }

    /**
     * @backupGlobals
     */
    public function testSpecialGithubPatternForCdnUrl()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';
        $configuredCdnUrl = 'https://github.com/medium/phantomjs';
        $_ENV['PHANTOMJS_CDNURL'] = $configuredCdnUrl;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame('https://github.com/medium/phantomjs/releases/download/v' . $version . '/', $cdnurl);
    }

    /**
     * @backupGlobals
     */
    public function testGetCdnUrlConfigPrecedence()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';

        // Test default URL is returned when there is no config
        $cdnurlExpected = Installer::PHANTOMJS_CDNURL_DEFAULT;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($cdnurlExpected, $cdnurl);

        // Test composer.json extra config overrides the default URL
        $cdnurlExpected = 'scheme://host/extra-url/';
        $extraData = array(Installer::PACKAGE_NAME => array('cdnurl' => $cdnurlExpected));
        $this->setUpForGetCdnUrl($extraData);
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($cdnurlExpected, $cdnurl);

        // Test $_SERVER var overrides default URL and extra config
        $cdnurlExpected = 'scheme://host/server-var-url/';
        $_SERVER['PHANTOMJS_CDNURL'] = $cdnurlExpected;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($cdnurlExpected, $cdnurl);

        // Test $_ENV var overrides default URL, extra config and $_SERVER var
        $cdnurlExpected = 'scheme://host/env-var-url/';
        $_ENV['PHANTOMJS_CDNURL'] = $cdnurlExpected;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($cdnurlExpected, $cdnurl);
    }

    public function testgetURL()
    {
        $this->setUpForGetCdnUrl();
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
