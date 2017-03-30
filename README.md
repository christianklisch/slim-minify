slim-minify
===========

Slim middleware to minify HTML output generated by the slim PHP framework. It removes whitespaces, empty lines, tabs
beetween html-tags and comments to reduce traffic. This script is a summary of stackoverflow answers.

## Usage

Copy the file Minify.php to 'Slim/Middleware/'. Register minify via $app->add():


or use the composer:
```
    "require": {
        "christianklisch/slim-minify": "0.5.0"
    }
```

in 'src/middleware.php':
```php
$app->add(new \Slim\Middleware\Minify() );
```

## Contributors

* Christian Klisch http://www.christian-klisch.de


## Copyright and license

Copyright 2014 released under [MIT](LICENSE) license.
