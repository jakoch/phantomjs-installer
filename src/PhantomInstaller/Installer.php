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

        # get global "required" packages array, to find the "phantomjs-installer" and fetch it's "version"
        $requiredPackagesArray = $package->getRequires();
        $phantomjsInstaller_PackageLink = $requiredPackagesArray['jakoch/phantomjs-installer'];
        $version = $phantomjsInstaller_PackageLink->getPrettyConstraint();

        # fallback to a hardcoded version number, if "dev-master" was set
        if($version === 'dev-master') {
            $version = '1.9.7';
        }

        #$io = $event->getIO();
        #$io->write('<info>Fetching PhantomJS v'.$version.'</info>');

        $name = self::PHANTOMJS_NAME;
        $url = self::getURL($version);

        # Create Composer In-Memory Package

        $targetDir = './vendor/jakoch/phantomjs';

        $versionParser = new VersionParser();
        $normVersion = $versionParser->normalize($version);
        $package = new Package($name, $normVersion, $version);

        $package->setTargetDir($targetDir);
        $package->setInstallationSource('dist');
        $package->setDistType(pathinfo($url, PATHINFO_EXTENSION) == 'zip' ? 'zip' : 'tar'); // set zip, tarball
        $package->setDistUrl($url);

        # Download the Archive

        $downloadManager = $event->getComposer()->getDownloadManager();
        $downloadManager->download($package, $targetDir, false);

        # Copy PhantomJS to "bin" folder

        self::recursiveCopy($targetDir, './bin');
    }

    /**
     * Recursive copy (with PHP default "overwrite on copy").
     *
     * @param $source Source folder.
     * @param $dest Destination folder.
     */
    public static function recursiveCopy($source, $dest)
    {
        if(is_dir($source) === true) {
            $dir = opendir($source);
            while($file = readdir($dir)) {
                if($file !== '.' && $file !== '..') {
                    if(is_dir($source.'/'.$file) === true) {
                        if(is_dir($dest.'/'.$file) === false) {
                            mkdir($dest.'/'.$file);
                        }
                        self::recursiveCopy($source.'/'.$file, $dest.'/'.$file);
                    } else {
                        copy($source.'/'.$file, $dest.'/'.$file);
                    }
                }
            }
            closedir($dir);
        } else {
            copy($source, $dest);
        }
    }

    /**
     * Returns the URL of the PhantomJS distribution for the installing OS.
     */
    public static function getURL($version)
    {
        $url = false;
        $os = self::getOS();

        // old versions up to v1.9.2 were hosted on https://phantomjs.googlecode.com/files/
        // newer versions are hosted on https://bitbucket.org/ariya/phantomjs/downloads/

        if ($os === 'windows') {
            $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' . $version . '-windows.zip';
        }

        if ($os === 'linux') {
            $bitsize = self::getBitSize();

            if ($bitsize === 32) {
                $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' .  $version . '-linux-i686.tar.bz2';
            }

            if ($bitsize === 64) {
                $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' .  $version . '-linux-x86_64.tar.bz2';
            }
        }

        if ($os === 'macosx') {
            $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' .  $version . '-macosx.zip';
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
