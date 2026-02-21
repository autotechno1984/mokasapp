<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Make the 'central' connection (used by Tenant model) share the same
        // underlying PDO connection as the default 'sqlite' connection.
        // This avoids "database is locked" when RefreshDatabase wraps sqlite
        // in a transaction and central tries to write to the same file.
        if (config('database.default') === 'sqlite') {
            DB::purge('central');
            DB::extend('central', fn () => DB::connection('sqlite'));
        }
    }
}
