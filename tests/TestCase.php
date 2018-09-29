<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Tests\Concerns\ManagesDatabase;
use Tests\Fakes\RegularMember;

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

        Relation::morphMap([
            'regular_member' => RegularMember::class,
        ]);
    }
}
