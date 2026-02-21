<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // When using SQLite, purge central connection so it reconnects
        // to the same database file as the default sqlite connection.
        if (config('database.default') === 'sqlite') {
            DB::purge('central');
        }
    }
}
