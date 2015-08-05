# PHP Assumptions

## Setup
```sh
$ composer require --dev rskuipers/php-assumptions=^0.1.0
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

## Future
This tool is the result of a proof of concept inspired by the "[From assumptions to assertions](http://rskuipers.com/entry/from-assumptions-to-assertions)" blog post.
I have some ideas to make it more context-aware and have it give suggestions or detect more cases of weak assumptions.