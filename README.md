# PHP Assumptions
[![Build Status](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/build.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/rskuipers/php-assumptions/?branch=master)

## Setup
```sh
$ composer require --dev rskuipers/php-assumptions
```

## Introduction
PHP Assumptions is the result of a proof of concept inspired by the "[From assumptions to assertions](http://rskuipers.com/entry/from-assumptions-to-assertions)" blog post.
It's a static code analysis tool doing checks for weak assumptions.

This is an example of an **assumption**:

```php
if ($user !== null) {
    $user->logout();
}
```

Running `bin/phpa` on this file would yield the following output:

```
----------------------------------------------
| file        | line | message               |
==============================================
| example.php | 3    | if ($user !== null) { |
----------------------------------------------

1 out of 1 boolean expressions are assumptions (100%)
```

This is an example of an **assertion**:

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
