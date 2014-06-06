phantomjs-installer
===================

[![Latest Stable Version](https://poser.pugx.org/jakoch/phantomjs-installer/version.png)](https://packagist.org/packages/jakoch/phantomjs-installer)
[![Total Downloads](https://poser.pugx.org/jakoch/phantomjs-installer/d/total.png)](https://packagist.org/packages/jakoch/phantomjs-installer)
[![Build Status](https://travis-ci.org/jakoch/phantomjs-installer.png)](https://travis-ci.org/jakoch/phantomjs-installer)
[![License](https://poser.pugx.org/jakoch/phantomjs-installer/license.png)](https://packagist.org/packages/jakoch/phantomjs-installer)

A Composer package which installs the PhantomJS binary (Linux, Windows, Mac) into `/bin` of your project.

## Installation

To install PhantomJS as a local, per-project dependency to your project, simply add a dependency on `jakoch/phantomjs-installer` to your project's `composer.json` file.

```json
{
    "require": {
        "jakoch/phantomjs-installer": "1.9.7"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```
For a development dependency, change `require` to `require-dev`:

```json
{
    "require-dev": {
        "jakoch/phantomjs-installer": "1.9.7"
    },
    "config": {
        "bin-dir": "bin"
    }
}
```

By setting the Composer configuration directive `bin-dir`, the [vendor binaries](https://getcomposer.org/doc/articles/vendor-binaries.md#can-vendor-binaries-be-installed-somewhere-other-than-vendor-bin-) will be installed into the defined folder.
**Important! Composer will install the binaries into `vendor\bin`, if you do not set the `bin-dir` to `bin`.**

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

Now, assuming that the scripts section is set up as required, the PhantomJS binary
will be installed into the `/bin` folder and updated alongside the project's Composer dependencies.

## How does this work internally?

1. **Fetching the PhantomJS Installer**
In your composer.json you require the package "phantomjs-installer".
The package is fetched by composer and stored into `./vendor/jakoch/phantomjs-installer`.
It contains only one file the `PhantomInstaller\\Installer`.

2. **Platform-specific download of PhantomJS**
The `PhantomInstaller\\Installer` is run as a "post-install-cmd". That's why you need the "scripts" section in your "composer.json".
The installer creates a new composer in-memory package "phantomjs",
detects your OS and downloads the correct Phantom version to the folder `./vendor/jakoch/phantomjs`.
All PhantomJS files reside there, especially the `examples`.

3. **Installation into `/bin` folder**
The binary is then copied from `./vendor/jakoch/phantomjs` to your composer configured `bin-dir` folder.
