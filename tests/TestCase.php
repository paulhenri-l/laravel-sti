<?php

namespace Tests;

use Tests\Concerns\ManagesDatabase;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use ManagesDatabase;

    /**
     * Prepare the DB and load a fresh schema for your test suite.
     */
    protected function setUp()
    {
        $this->prepareDbIfNecessary();
        $this->freshSchema();
    }
}
