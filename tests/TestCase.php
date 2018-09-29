<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Tests\Concerns\ManagesDatabase;
use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
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

    /**
     * Helper to count the number of time an object of a type has been seen.
     */
    protected function updateMemberCount(Member $member, array &$results)
    {
        if ($member instanceof PremiumMember) {
            $results['premium_count']++;
        } elseif ($member instanceof RegularMember) {
            $results['regular_count']++;
        }
    }
}
