# PHP Assumptions
[![Build Status](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/build.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/?branch=master)

## Setup
```sh
$ composer require --dev rskuipers/php-assumptions
```

## Introduction
This is a static code analysis tool doing some basic checks for weak assumptions.

This is an example of a **weak assumption**:

```php
if ($user !== null) {
    $user->logout();
}
```

Running `bin/phpa` on this file would yield the following output:

```
example.php:3: if ($user !== null) {
```

This is an example of a **strong assertion**:

```php
if ($user instanceof User) {
    $user->logout();
}
```

## Tests
This project is built with [PHPUnit](https://github.com/sebastianbergmann/phpunit) and [Prophecy](https://github.com/phpspec/prophecy-phpunit).
In order to run these tests make sure you have dev dependencies installed with composer.

Running PHPUnit:
```sh
$ ./vendor/bin/phpunit
```

## Future
This tool is the result of a proof of concept inspired by the "[From assumptions to assertions](http://rskuipers.com/entry/from-assumptions-to-assertions)" blog post.
I have some ideas to make it more context-aware and have it give suggestions or detect more cases of weak assumptions.