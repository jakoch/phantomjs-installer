<?php

/*
 * This file is part of the "jakoch/phantomjs-installer" package.
 *
 * Copyright (c) 2013-2014 Jens-André Koch <jakoch@web.de>
 *
 * The content is released under the MIT License. Please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PhantomInstaller;

use Composer\Script\Event;
use Composer\Composer;

use Composer\Package\Package;
use Composer\Package\RootPackageInterface;
use Composer\Package\Version\VersionParser;

class Installer
{
    const PHANTOMJS_NAME = 'PhantomJS';

    const PHANTOMJS_TARGETDIR = './vendor/jakoch/phantomjs';

    /**
     * Operating system dependend installation of PhantomJS
     */
    public static function installPhantomJS(Event $event)
    {
        $composer = $event->getComposer();

        $version = self::getVersion($composer);

        $url = self::getURL($version);

        // Create Composer In-Memory Package

        $versionParser = new VersionParser();
        $normVersion = $versionParser->normalize($version);

        $package = new Package(self::PHANTOMJS_NAME, $normVersion, $version);
        $package->setTargetDir(self::PHANTOMJS_TARGETDIR);
        $package->setInstallationSource('dist');
        $package->setDistType(pathinfo($url, PATHINFO_EXTENSION) === 'zip' ? 'zip' : 'tar'); // set zip, tarball
        $package->setDistUrl($url);

        // Download the Archive

        //$io = $event->getIO();
        //$io->write('<info>Fetching PhantomJS v'.$version.'</info>');

        $downloadManager = $composer->getDownloadManager();
        $downloadManager->download($package, self::PHANTOMJS_TARGETDIR, false);

        // Copy all PhantomJS files to "bin" folder
        // self::recursiveCopy(self::PHANTOMJS_TARGETDIR, './bin');

        // Copy only the PhantomJS binary to the "bin" folder

        self::copyPhantomJsBinaryToBinFolder($composer);
    }

    /**
     * Returns the PhantomJS version number.
     *
     * Firstly, we search for a version number in the local repository,
     * secondly, in the root package.
     * A version specification of "dev-master#<commit-reference>" is disallowed.
     *
     * @param Composer $composer
     * @return string $version Version
     */
    public static function getVersion($composer)
    {
        $packages = $composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();

        foreach($packages as $package) {
            if($package->getName() === 'jakoch/phantomjs-installer') {
                $version = $package->getPrettyVersion();
            }
        }

        // version was not found in the local repository, let's take a look at the root package
        if($version == null) {
            $version = self::getRequiredVersion($composer->getPackage());
        }

        // grab version from commit-reference, e.g. "dev-master#<commit-ref> as version"
        if(preg_match('/dev-master#(?:.*)(\d.\d.\d)/i', $version, $matches)) {
            $version = $matches[1];
        }

        // fallback to a hardcoded version number, if "dev-master" was set
        if ($version === 'dev-master') {
            $version = '1.9.8';
        }

        return $version;
    }

    /**
     * Returns the version for the given package either from the "require" or "require-dev" packages array.
     *
     * @param RootPackageInterface $package
     * @param string $packageName
     * @throws \RuntimeException
     * @return mixed
     */
    public static function getRequiredVersion(RootPackageInterface $package, $packageName = 'jakoch/phantomjs-installer')
    {
        foreach (array($package->getRequires(), $package->getDevRequires()) as $requiredPackages) {
            if (isset($requiredPackages[$packageName])) {
                return $requiredPackages[$packageName]->getPrettyConstraint();
            }
        }
        throw new \RuntimeException('Can not determine required version of ' . $packageName);
    }

    /**
     * Copies the PhantomJs binary to the bin folder.
     * Takes different "folder structure" of the archives and different "binary file names" into account.
     */
    public static function copyPhantomJsBinaryToBinFolder(Composer $composer)
    {
        $composerBinDir = $composer->getConfig()->get('bin-dir');
        if (!is_dir($composerBinDir)) {
            mkdir($composerBinDir);
        }

        $os = self::getOS();

        $sourceName = '/bin/phantomjs';
        $targetName = $composerBinDir . '/phantomjs';

        if ($os === 'windows') { // no bin folder on windows and suffix: .exe
            $sourceName = '/phantomjs.exe';
            $targetName = $composerBinDir . '/phantomjs.exe';
        }

        if ($os !== 'unknown') {
            copy(self::PHANTOMJS_TARGETDIR . $sourceName, $targetName);
            chmod($targetName, 0755);
        }
    }

    /**
     * Recursive copy of files and folders (with PHP default "overwrite on copy").
     *
     * @param $source Source folder.
     * @param $dest Destination folder.
     */
    /*public static function recursiveCopy($source, $dest)
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
    }*/

    /**
     * Returns the URL of the PhantomJS distribution for the installing OS.
     *
     * @param string $version
     * @return string Download URL
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
                $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' . $version . '-linux-i686.tar.bz2';
            }

            if ($bitsize === 64) {
                $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' . $version . '-linux-x86_64.tar.bz2';
            }
        }

        if ($os === 'macosx') {
            $url = 'https://bitbucket.org/ariya/phantomjs/downloads/phantomjs-' . $version . '-macosx.zip';
        }

        # OS unknown
        if ($url === false) {
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
