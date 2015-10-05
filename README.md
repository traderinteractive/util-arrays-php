# util-arrays-php
[![Build Status](https://travis-ci.org/dominionenterprises/util-arrays-php.svg?branch=master)](https://travis-ci.org/dominionenterprises/util-arrays-php)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/dominionenterprises/util-arrays-php.svg?style=flat)](https://scrutinizer-ci.com/g/dominionenterprises/util-arrays-php/)
[![Coverage Status](https://coveralls.io/repos/dominionenterprises/util-arrays-php/badge.svg?branch=master&service=github)](https://coveralls.io/github/dominionenterprises/util-arrays-php?branch=master)

[![Latest Stable Version](http://img.shields.io/packagist/v/dominionenterprises/util-arrays.svg?style=flat)](https://packagist.org/packages/dominionenterprises/util-arrays)
[![Total Downloads](http://img.shields.io/packagist/dt/dominionenterprises/util-arrays.svg?style=flat)](https://packagist.org/packages/dominionenterprises/util-arrays)
[![License](http://img.shields.io/packagist/l/dominionenterprises/util-arrays.svg?style=flat)](https://packagist.org/packages/dominionenterprises/util-arrays)

A collection of utilities for working with PHP arrays.

## Requirements

util-arrays-php requires PHP 5.4 (or later).

##Composer
To add the library as a local, per-project dependency use [Composer](http://getcomposer.org)! Simply add a dependency on `dominionenterprises/util-arrays` to your project's `composer.json` file such as:

```json
{
    "require": {
        "dominionenterprises/util-arrays": "~1.0"
    }
}
```
##Documentation
Found in the [source](src) itself, take a look!

##Contact
Developers may be contacted at:

 * [Pull Requests](https://github.com/dominionenterprises/util-arrays-php/pulls)
 * [Issues](https://github.com/dominionenterprises/util-arrays-php/issues)

##Project Build
With a checkout of the code get [Composer](http://getcomposer.org) in your PATH and run:

```sh
./build.php
```

There is also a [docker](http://www.docker.com/)-based
[fig](http://www.fig.sh/) configuration that will execute the build inside a docker container.  This is an easy way to build the application:
```sh
fig run build
```

For more information on our build process, read through out our [Contribution Guidelines](CONTRIBUTING.md).
