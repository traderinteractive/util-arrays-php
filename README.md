# util-arrays-php

[![Build Status](https://travis-ci.org/traderinteractive/util-arrays-php.svg?branch=master)](https://travis-ci.org/traderinteractive/util-arrays-php)
[![Code Quality](https://scrutinizer-ci.com/g/traderinteractive/util-arrays-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/traderinteractive/util-arrays-php/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/traderinteractive/util-arrays-php/badge.svg?branch=master)](https://coveralls.io/github/traderinteractive/util-arrays-php?branch=master)

[![Latest Stable Version](https://poser.pugx.org/traderinteractive/util-arrays/v/stable)](https://packagist.org/packages/traderinteractive/util-arrays)
[![Latest Unstable Version](https://poser.pugx.org/traderinteractive/util-arrays/v/unstable)](https://packagist.org/packages/traderinteractive/util-arrays)
[![License](https://poser.pugx.org/traderinteractive/util-arrays/license)](https://packagist.org/packages/traderinteractive/util-arrays)

[![Total Downloads](https://poser.pugx.org/traderinteractive/util-arrays/downloads)](https://packagist.org/packages/traderinteractive/util-arrays)
[![Monthly Downloads](https://poser.pugx.org/traderinteractive/util-arrays/d/monthly)](https://packagist.org/packages/traderinteractive/util-arrays)
[![Daily Downloads](https://poser.pugx.org/traderinteractive/util-arrays/d/daily)](https://packagist.org/packages/traderinteractive/util-arrays)

A collection of utilities for working with PHP arrays.

## Requirements

util-arrays-php requires PHP 7.0 (or later).

## Composer
To add the library as a local, per-project dependency use [Composer](http://getcomposer.org)! Simply add a dependency on
`traderinteractive/util-arrays` to your project's `composer.json` file such as:

```sh
composer require traderinteractive/util-arrays
```

## Documentation

Found in the [source](src) itself, take a look!

## Contact

Developers may be contacted at:

 * [Pull Requests](https://github.com/traderinteractive/util-arrays-php/pulls)
 * [Issues](https://github.com/traderinteractive/util-arrays-php/issues)

## Project Build

With a checkout of the code get [Composer](http://getcomposer.org) in your PATH and run:

```sh
./vendor/bin/phpunit
./vendor/bin/phpcs
```

There is also a [docker](http://www.docker.com/)-based [fig](http://www.fig.sh/) configuration that will execute the build inside a docker container.  This is an easy way to build the application:

```sh
fig run build
```

For more information on our build process, read through out our [Contribution Guidelines](.github/CONTRIBUTING.md).
