# ChangeLog

## [Unreleased]

- "It was a bright day in April, and the clocks were striking thirteen." - 1984

## [3.0.2] - 2024-06-26

- fixed installer tests after branch rename dev-master -> dev-main
- removed Travis CI config
- added Github Actions + dependabot config
- updated Devcontainer and Dockerfile

## [3.0.1] - 2021-09-03

- [Issue #51](https://github.com/jakoch/phantomjs-installer/issues/51): Fix TypeError thrown during install

## [3.0.0] - 2020-07-18

- [Issue #47](https://github.com/jakoch/phantomjs-installer/issues/47): Composer v2 support
- Removed direct mapping of package version to PhantomJS version (!)
  - The search order for the PhantomJS version changed to:
    1. $_ENV['PHANTOMJS_VERSION']
    2. $_SERVER['PHANTOMJS_VERSION']
    3. composer.json extra section, e.g. `"extra": { "jakoch/phantomjs-installer": { "phantomjs-version": "2.1.1" } }`
    4. fallback to v2.1.1 (hardcoded latest version)
- raise PHPUnit version to ^8 (for PHP 7.2-7.4)
- Travis: stopped testing on PHP 5.x & 7.1, added testing PHP 7.4

## [2.1.1-p09] - 2017-08-16

- [Issue #18](https://github.com/jakoch/phantomjs-installer/issues/18): Stop/kill (and restart?) PhantomJS before updating it.
  - Fixed replacing the binary of a running process by copying the binary to a temporary file, then renaming it.
- [Issue #41](https://github.com/jakoch/phantomjs-installer/issues/41): 0770 chmod status.
  - removed const PHANTOMJS_CHMOD and hardcoded `0777 & ~umask()`
- Travis: stopped testing on HHVM, added testing on PHP7.1

## [2.1.1-p08] - 2017-01-10

- [Issue #42](https://github.com/jakoch/phantomjs-installer/issues/42): fix silent TransportExceptions (handle all TransportExceptions with HttpStatusCode != 404)

## [2.1.1-p07] - 2016-10-12

- general code cleanup / refactoring / removed static functions
- [Issue #35](https://github.com/jakoch/phantomjs-installer/issues/35): Unable to install / bitbucket failures
  - you might now use the config section extra of your `composer.json` file to set the CDN_URL
  - changed the default download location for BitBucket to https://api.bitbucket.org/2.0/ (API v2)

## [2.1.1-p06] - 2016-08-09

- [Issue #34](https://github.com/jakoch/phantomjs-installer/issues/34): Bitbucket downloading issue
  - added env and server variable `PHANTOMJS_CDNURL` to set a mirror as download location
- added `$_SERVER` variable handling for all `$_ENV` vars
  this enables you to use either a server or env var for `PHANTOMJS_PLATFORM`, `PHANTOMJS_BITSIZE` and `PHANTOMJS_CDNURL`

## [2.1.1-p05] - 2016-07-11

- [Issue #32](https://github.com/jakoch/phantomjs-installer/issues/32): do not download multiple times, when bz2 extension isn't loaded

## [2.1.1-p04] - 2016-06-27

- [Issue #17](https://github.com/jakoch/phantomjs-installer/issues/17): Do not try to re-install same PhantomJS version
  - The installation is skipped, when PhantomJS is already installed and the requested version is not higher

## [2.1.1-p03] - 2016-05-22

- [Issue #30](https://github.com/jakoch/phantomjs-installer/issues/30): Issue installing PJS on Amazon Linux In Docker locally
  - added variables $_ENV['PHANTOMJS_PLATFORM'] and $_ENV['PHANTOMJS_BITSIZE'] to override platform requirements
    this allows to package on a platform different to the target platform, e.g. package on MacOSX for Linux or on Windows for MacOSX.
- identify `FreeBSD` and `OpenBSD` as `MacOSX` - they will use MacOSX downloads

## [2.1.1-p02] - 2016-05-12

- [Fix #29](https://github.com/jakoch/phantomjs-installer/issues/29): Invalid version string "^2.1"

## [2.1.1-p01] - 2016-04-12

- [PR #28](https://github.com/jakoch/phantomjs-installer/pull/28): added PHP "ext-bz2" as requirement and catch only exceptions that will be handled
- [PR #27](https://github.com/jakoch/phantomjs-installer/pull/27): use static to access chmod constant

## [2.1.1] - 2016-01-25

- added v2.1.1 to the PhantomJS versions array to
- Automatic download retrying with version lowering, if download fails with 404
- class `PhantomInstaller\PhantomBinary` is created automatically during installation,
  to access the binary and its folder more easily
- added support Composer patch version tag with a patch level, like "2.1.1-p02"
- added usage examples (inside `/test`), each with a different `composer.json` file
- add support for vendor-dir as installation folder for the extracted "phantomjs"

## [2.0.0] - 2014-08-09

## [1.9.8] - 2014-07-10

## [1.9.7] - 2014-06-24

- Initial Release
- grab version number from explicit commit references, issue #8

[Unreleased]: https://github.com/jakoch/phantomjs-installer/compare/3.0.1...HEAD
[3.0.1]: https://github.com/jakoch/phantomjs-installer/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p09...3.0.0
[2.1.1-p09]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p08...2.1.1-p09
[2.1.1-p08]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p07...2.1.1-p08
[2.1.1-p07]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p06...2.1.1-p07
[2.1.1-p06]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p05...2.1.1-p06
[2.1.1-p05]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p04...2.1.1-p05
[2.1.1-p04]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p03...2.1.1-p04
[2.1.1-p03]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p02...2.1.1-p03
[2.1.1-p02]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p01...2.1.1-p02
[2.1.1-p01]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1...2.1.1-p01
[2.1.1]: https://github.com/jakoch/phantomjs-installer/compare/2.0.0...2.1.1
[2.0.0]: https://github.com/jakoch/phantomjs-installer/compare/1.9.8...2.0.0
[1.9.8]: https://github.com/jakoch/phantomjs-installer/compare/1.9.7...1.9.8
[1.9.7]: https://github.com/jakoch/phantomjs-installer/releases/tag/1.9.7
