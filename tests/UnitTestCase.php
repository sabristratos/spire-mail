<?php

namespace SpireMail\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use SpireMail\SpireMailServiceProvider;

abstract class UnitTestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SpireMailServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('spire-mail.merge_tags', [
            'app_name' => 'Test App',
            'app_url' => 'https://test.com',
            'current_year' => '2024',
        ]);
    }
}
