<?php

namespace PhantomInstaller\Test;

use PhantomInstaller\Installer;
use PhantomInstaller\PhantomBinary;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\BackupStaticProperties;

#[CoversClass(\PhantomInstaller\Installer::class)]
#[BackupStaticProperties(true)]
class InstallerTest extends TestCase
{
    /** @var Installer */
    protected $object;

    protected $bakEnvVars = array();

    protected $bakServerVars = array();

    protected function setUp(): void
    {
        parent::setUp();

        $mockComposer = $this->getMockComposer();
        $mockIO = $this->getMockIO();
        $this->object = new Installer($mockComposer, $mockIO);

        // Backup $_ENV and $_SERVER
        $this->bakEnvVars = $_ENV;
        $this->bakServerVars = $_SERVER;
    }

    protected function tearDown(): void
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

    #[CoversClass(\PhantomInstaller\PhantomBinary::class)]
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

    #[BackupGlobals(true)]
    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testCdnUrlTrailingSlash()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';
        $configuredCdnUrl = 'scheme://host/path'; // without slash
        $_ENV['PHANTOMJS_CDNURL'] = $configuredCdnUrl;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertMatchesRegularExpression('{(?:^|[^/])/$}', $cdnurl, 'CdnUrl should end with one slash.');
    }

    #[BackupGlobals(true)]
    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testSpecialGithubPatternForCdnUrl()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';

        // Test rewrite for the Medium url as documented
        $configuredCdnUrl = 'https://github.com/Medium/phantomjs';
        $_ENV['PHANTOMJS_CDNURL'] = $configuredCdnUrl;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($configuredCdnUrl . '/releases/download/v' . $version . '/', $cdnurl);

        // Test that a longer url is not rewritten
        $configuredCdnUrl = 'https://github.com/Medium/phantomjs/releases/download/v1.9.19/';
        $_ENV['PHANTOMJS_CDNURL'] = $configuredCdnUrl;
        $cdnurl = $this->object->getCdnUrl($version);
        $this->assertSame($configuredCdnUrl, $cdnurl);
    }

    #[BackupGlobals(true)]
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

    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testGetVersionFromExtra()
    {
        $expectedVersion = '1.9.8';
        $extraData = array(Installer::PACKAGE_NAME => array('phantomjs-version' => $expectedVersion));
        $this->setUpForGetCdnUrl($extraData);
        $version = $this->object->getVersion();
        $this->assertSame($expectedVersion, $version);
    }

    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testGetURL()
    {
        $this->setUpForGetCdnUrl();
        $version = '1.0.0';
        $url = $this->object->getURL($version);
        $this->assertTrue(is_string($url));
    }

    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testGetOS()
    {
        $os = $this->object->getOS();
        $this->assertTrue(is_string($os));
    }

    #[CoversClass(\PhantomInstaller\Installer::class)]
    public function testGetBitSize()
    {
        $bitsize = $this->object->getBitSize();
        $this->assertTrue(is_string($bitsize));
    }
}
