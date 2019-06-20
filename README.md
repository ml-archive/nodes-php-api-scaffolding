## API Scaffolding

⚠️**This package is deprecated**⚠️

A package which scaffolds the most common stuff used in a [Nodes API](http://nodesagency.com) project.

[![Total downloads](https://img.shields.io/packagist/dt/nodes/api-scaffolding.svg)](https://packagist.org/packages/nodes/api-scaffolding)
[![Monthly downloads](https://img.shields.io/packagist/dm/nodes/api-scaffolding.svg)](https://packagist.org/packages/nodes/api-scaffolding)
[![Latest release](https://img.shields.io/packagist/v/nodes/api-scaffolding.svg)](https://packagist.org/packages/nodes/api-scaffolding)
[![Open issues](https://img.shields.io/github/issues/nodes-php/api-scaffolding.svg)](https://github.com/nodes-php/api-scaffolding/issues)
[![License](https://img.shields.io/packagist/l/nodes/api-scaffolding.svg)](https://packagist.org/packages/nodes/api-scaffolding)
[![Star repository on GitHub](https://img.shields.io/github/stars/nodes-php/api-scaffolding.svg?style=social&label=Star)](https://github.com/nodes-php/api-scaffolding/stargazers)
[![Watch repository on GitHub](https://img.shields.io/github/watchers/nodes-php/api-scaffolding.svg?style=social&label=Watch)](https://github.com/nodes-php/api-scaffolding/watchers)
[![Fork repository on GitHub](https://img.shields.io/github/forks/nodes-php/api-scaffolding.svg?style=social&label=Fork)](https://github.com/nodes-php/api-scaffolding/network)
[![StyleCI](https://styleci.io/repos/62562441/shield)](https://styleci.io/repos/62562441)

## 📝 Introduction

In [Nodes](http://nodesagency.com) we create a lot of API's for our mobile applications.

So to save time, we made this package which scaffolds the most common used controllers, models, repositories, validators etc. that we use in all our projects.

## 📦 Installation

To install this package you will need:

* Laravel 5.1+
* PHP 5.5.9+

You must then modify your `composer.json` file and run `composer update` to include the latest version of the package in your project.

```json
"require": {
    "nodes/api-scaffolding": "^1.0"
}
```

Or you can run the composer require command from your terminal.

```bash
composer require nodes/api-scaffolding:^1.0
```

## 🔧 Setup

Setup service providers in `config/app.php`

```php
Nodes\Api\Scaffolding\ServiceProvider::class,
```


## ⚙ Usage

Run the Artisan command:
```bash
php artisan nodes:api:scaffolding
```

If you only wish to scaffold the reset password stuff, then you can run the Artisan command:
```bash
php artisan nodes:api:reset-password
```

## 🏆 Credits

This package is developed and maintained by the PHP team at [Nodes](http://nodesagency.com)

[![Follow Nodes PHP on Twitter](https://img.shields.io/twitter/follow/nodesphp.svg?style=social)](https://twitter.com/nodesphp) [![Tweet Nodes PHP](https://img.shields.io/twitter/url/http/nodesphp.svg?style=social)](https://twitter.com/nodesphp)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
