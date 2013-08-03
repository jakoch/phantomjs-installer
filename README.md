phantomjs-installer
===================

A Composer package which installs the PhantomJS binary (Linux, Windows, Mac) into `/bin` of your project.

## Installation

To install PhantomJS as a local, per-project dependency to your project, simply add a dependency on `jakoch/phantomjs-installer` to your project's `composer.json` file.

```json
{
    "require": {
        "jakoch/phantomjs-installer": "1.9.1"
    }
}
```

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
