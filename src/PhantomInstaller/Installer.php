<?php
namespace PhantomInstaller;

use Composer\Script\Event;

use Composer\Package\Package;
use Composer\Package\Version\VersionParser;

class Installer
{
    const PHANTOMJS_NAME = 'PhantomJS';

    /**
     * Operating system dependend installation of PhantomJS
     */
    public static function installPhantomJS(Event $event)
    {
        $composer = $event->getComposer();
        $package = $composer->getPackage();

        # get global "required" packges array to find the "phantomjs-installer" and fetch it's "version"
        $requiredPackagesArray = $package->getRequires();
        $phantomjsInstaller_PackageLink = $requiredPackagesArray['jakoch/phantomjs-installer'];
        $version = $phantomjsInstaller_PackageLink->getPrettyConstraint();

        # fallback to a harccoded version number, if "dev-master" was set
        if($version === 'dev-master') {
            $version = '1.9.1';
        }

        #$io = $event->getIO();
        #$io->write('<info>Fetching PhantomJS v'.$version.'</info>');

        $name = self::PHANTOMJS_NAME;
        $url = self::getURL($version);

        # Creating Composer In-Memory Package

        $targetDir = './bin';

        $versionParser = new VersionParser();
        $normVersion = $versionParser->normalize($version);
        $package = new Package($name, $normVersion, $version);

        $package->setTargetDir($targetDir);
        $package->setInstallationSource('dist');
        $package->setDistType(pathinfo($url, PATHINFO_EXTENSION) == 'zip' ? 'zip' : 'tar'); // set zip, tarball
        $package->setDistUrl($url);

        # Downloading the Archive

        $downloadManager = $event->getComposer()->getDownloadManager();
        $downloadManager->download($package, $targetDir, false);
    }

    /**
     * Returns the URL of the PhantomJS distribution for the installing OS.
     */
    public static function getURL($version)
    {
        $url = false;

        if (self::getOS() === 'windows') {
            $url = 'https://phantomjs.googlecode.com/files/phantomjs-' . $version . '-windows.zip';
        }

        if (self::getOS() === 'linux') {
            if (self::getBitSize() === 32) {
                $url = 'https://phantomjs.googlecode.com/files/phantomjs-' .  $version . '-linux-i686.tar.bz2';
            }

            if (self::getBitSize() === 64) {
                $url = 'https://phantomjs.googlecode.com/files/phantomjs-' .  $version . '-linux-x86_64.tar.bz2';
            }
        }

        if (self::getOS() === 'macosx') {
            $url = 'https://phantomjs.googlecode.com/files/phantomjs-' .  $version . '-macosx.zip';
        }

        # OS unknown
        if($url === false) {
            throw new \RuntimeException(
                'The Installer could not select a PhantomJS package for this OS.
                Please install PhantomJS manually into the /bin folder of your project.'
            );
        }

        return $url;
    }

    /**
     * Returns the Operating System.
     *
     * @return string OS, e.g. macosx, windows, linux.
     */
    public static function getOS()
    {
        $uname = strtolower(php_uname());

        if (strpos($uname, "darwin") !== false) {
            return 'macosx';
        } elseif (strpos($uname, "win") !== false) {
            return 'windows';
        } elseif (strpos($uname, "linux") !== false) {
            return 'linux';
        } else {
            return 'unknown';
        }
    }

    /**
     * Returns the Bit-Size.
     *
     * @return string BitSize, e.g. 32, 64.
     */
    public static function getBitSize()
    {
        if (PHP_INT_SIZE === 4) {
            return 32;
        }

        if (PHP_INT_SIZE === 8) {
            return 64;
        }

        return PHP_INT_SIZE; // 16-bit?
    }
}
