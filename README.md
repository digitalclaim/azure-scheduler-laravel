# This is my package azure-scheduler-laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digitalclaim/azure-scheduler-laravel.svg?style=flat-square)](https://packagist.org/packages/digitalclaim/azure-scheduler-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/digitalclaim/azure-scheduler-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/digitalclaim/azure-scheduler-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/digitalclaim/azure-scheduler-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/digitalclaim/azure-scheduler-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/digitalclaim/azure-scheduler-laravel.svg?style=flat-square)](https://packagist.org/packages/digitalclaim/azure-scheduler-laravel)

Laravel scheduler triggered by Azure Scheduled Functions. Instead of a CronJob the scheduled cloud function will request a callback url which triggers the Laravel scheduler.

This package is inspired by [stackkit/laravel-google-cloud-scheduler](https://github.com/stackkit/laravel-google-cloud-scheduler)

Warning: The callback route is rate limited to only one request per limit. Laravel sub-minute tasks are not supported.

## Installation

You can install the package via composer:

```bash
composer require digitalclaim/azure-scheduler-laravel
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="azure-scheduler-laravel-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

1. [Optional] Whitelist route for maintenance mode. This step is optional, but highly recommended. To allow jobs to keep running if the application is down (`php artisan down`) you must modify the `PreventRequestsDuringMaintenance` middleware:

```diff
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
+        '/handle-scheduler',
    ];
}
```

2. [Optional] Set application RUNNING_IN_CONSOLE (highly recommended). Some Laravel service providers only register their commands if the application is being accessed through the command line (Artisan). Because we are calling Laravel scheduler from a HTTP call, that means some commands may never register, such as the Laravel Scout command. To circumvent this, please add the following to `public/index.php`:

```diff
/*
|--------------------------------------------------------------------------
| Check If Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is maintenance / demo mode via the "down" command we
| will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}
+
+ /*
+ |--------------------------------------------------------------------------
+ | Manually Set Running In Console for Google Cloud Scheduler
+ |--------------------------------------------------------------------------
+ |
+ | Some service providers only register their commands if the application
+ | is running from the console. Since we are calling Cloud Scheduler
+ | from the browser we must manually trick the application into
+ | thinking that it is being run from the command line.
+ |
+ */
+
+ if (($_SERVER['REQUEST_URI'] ?? '') === '/cloud-scheduler-job') {
+     $_ENV['APP_RUNNING_IN_CONSOLE'] = true;
+ }
+
/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';
```

Copy the code here:

```php
/*
|--------------------------------------------------------------------------
| Manually Set Running In Console for Azure Scheduler
|--------------------------------------------------------------------------
|
| Some service providers only register their commands if the application
| is running from the console. Since we are calling Azure Scheduler
| from the browser we must manually trick the application into
| thinking that it is being run from the command line.
|
*/

if (($_SERVER['REQUEST_URI'] ?? '') === '/handle-scheduler') {
    $_ENV['APP_RUNNING_IN_CONSOLE'] = true;
}
```

3. Create a new scheduled Azure Function (nodejs) that triggeres every minute `0 * * * * *`:

```javascript
const axios = require("axios");

module.exports = async function (context, myTimer) {
    try {
        const response = await axios.get(
            "https://YOURSITE.azurewebsites.net/handle-scheduler"
        );

        context.log(response.data);
    } catch (error) {
        // If the promise rejects, an error will be thrown and caught here
        context.log(error);
    }

    context.done();
};
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Milo Tischler](https://github.com/milo)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
