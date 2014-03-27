phantomjs-installer
===================

[![Latest Stable Version](https://poser.pugx.org/jakoch/phantomjs-installer/version.png)](https://packagist.org/packages/jakoch/phantomjs-installer)
[![Total Downloads](https://poser.pugx.org/jakoch/phantomjs-installer/d/total.png)](https://packagist.org/packages/jakoch/phantomjs-installer)
[![Build Status](https://travis-ci.org/jakoch/phantomjs-installer.png)](https://travis-ci.org/jakoch/phantomjs-installer)

A Composer package which installs the PhantomJS binary (Linux, Windows, Mac) into `/bin` of your project.

## Installation

To install PhantomJS as a local, per-project dependency to your project, simply add a dependency on `jakoch/phantomjs-installer` to your project's `composer.json` file.

```json
{
    "require": {
        "jakoch/phantomjs-installer": "1.9.7"
    }
}
```

The version number of the package specifies the PhantomJS version!
"dev-master" is "v1.9.7".
The download source used is: https://bitbucket.org/ariya/phantomjs/downloads/

Currently Composer does not pass events to the handler scripts of dependencies.
You might execute the installer a) manually or b) by adding the following additional settings to your `composer.json`:

```json
{
    "scripts": {
        "post-install-cmd": [
            "PhantomInstaller\\Installer::installPhantomJS"
        ],
        "post-update-cmd": [
            "PhantomInstaller\\Installer::installPhantomJS"
        ]
    }
}
```

Now, assuming that the scripts section is set up as required, PhantomJS binaries
will be installed into the `/bin` folder and updated alongside the project's Composer dependencies.

## How does this work internally?

1. **Fetching the PhantomJS Installer**
In your composer.json you require the package "phantomjs-installer".
The package is fetched by composer and stored into `./vendor/jakoch/phantomjs-installer`.
It contains only one file the `PhantomInstaller\\Installer`.

2. **Platform-specific download of PhantomJS**
The `PhantomInstaller\\Installer` is run as a "post-install-cmd". That's way you need the "scripts" section in your "composer.json". The installer creates a new composer in-memory package "phantomjs", detects your OS and downloads the correct Phantom version to the folder `./vendor/jakoch/phantomjs`. 

3. **Installation into `/bin` folder**
The content is copied from `./vendor/jakoch/phantomjs` to your `/bin` folder, overwriting existing Phantom files, but leaving the rest of your `/bin` content untouched.
