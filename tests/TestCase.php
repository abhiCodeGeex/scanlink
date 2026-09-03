<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        // Never let Docker's environment override phpunit.xml. Every automated test
        // must boot against an isolated in-memory sqlite DB and an in-memory (array)
        // cache — the container sets CACHE_STORE=redis, and phpunit.xml's array value
        // is not forced, so persisted settings (e.g. label / form-builder prices) would
        // otherwise leak between test runs.
        foreach ([
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'DB_URL' => '',
            'CACHE_STORE' => 'array',
        ] as $key => $value) {
            putenv($key.'='.$value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        return parent::createApplication();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // RefreshDatabase resets the DB but not the cache; flush so cached settings
        // (Setting::valueFor) never leak between tests.
        Cache::flush();
    }
}
