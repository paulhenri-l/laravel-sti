<?php

use Illuminate\Database\Schema\Blueprint;

/**
 * By simply autoloading this file thanks to composer we can now
 * use this `type` macro in the SchemaBuilder without having to
 * either load it manually or use a service provider.
 *
 * This macro is used in the Tests\Concerns\ManagesDatabase trait
 * so even though it is not directly tested the whole test suite
 * relies on it and would break if it was broken.
 */

Blueprint::macro('type', function (string $columnName = 'type') {
    $this->string($columnName);
});
