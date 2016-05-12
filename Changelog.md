# ChangeLog

## [Unreleased]

- "It was a bright day in April, and the clocks were striking thirteen." - 1984

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

[Unreleased]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p02...HEAD
[2.1.1-p02]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1-p01...2.1.1-p02
[2.1.1-p01]: https://github.com/jakoch/phantomjs-installer/compare/2.1.1...2.1.1-p01
[2.1.1]: https://github.com/jakoch/phantomjs-installer/compare/2.0.0...2.1.1
[2.0.0]: https://github.com/jakoch/phantomjs-installer/compare/1.9.8...2.0.0
[1.9.8]: https://github.com/jakoch/phantomjs-installer/compare/1.9.7...1.9.8
[1.9.7]: https://github.com/jakoch/phantomjs-installer/releases/tag/1.9.7
