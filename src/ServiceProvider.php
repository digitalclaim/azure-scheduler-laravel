<?php

namespace DigitalClaim\AzureScheduler;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\RateLimiter;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * ServiceProvider
 */
class ServiceProvider extends PackageServiceProvider
{
    /**
     * configurePackage
     *
     * @param  mixed  $package
     */
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('azure-scheduler-laravel')
            ->hasRoute('web');
    }

    /**
     * bootingPackage
     *
     * @return void
     */
    public function bootingPackage()
    {
        RateLimiter::for('azure-scheduler', function (Request $request) {
            return Limit::perMinute(1)->by('minute_'.(Date::now())->format('i'));
        });
    }
}
