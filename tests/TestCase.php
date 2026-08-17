<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Force a deterministic testing environment before the application boots.
     *
     * Some shells (e.g. Git Bash on Windows) export APP_ENV / SESSION_* variables,
     * which would otherwise leak into the test suite and enable CSRF or the
     * database session driver during tests.
     */
    protected function setUp(): void
    {
        foreach ([
            'APP_ENV' => 'testing',
            'SESSION_DRIVER' => 'array',
            'DB_CONNECTION' => 'sqlite',
            'DB_DATABASE' => ':memory:',
            'CACHE_STORE' => 'array',
            'MAIL_MAILER' => 'array',
            'QUEUE_CONNECTION' => 'sync',
            'BCRYPT_ROUNDS' => '4',
        ] as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        parent::setUp();
    }
}
