<?php

namespace Tests\Unit;

use Tests\Fakes\Member;
use Tests\Fakes\Plan;
use Tests\Fakes\RegularMember;
use Tests\Fakes\Subscription;
use Tests\Fakes\PremiumMember;
use Tests\TestCase;

class RelationshipsTest extends TestCase
{
    /**
     * Test that belongs to return the correct sub type.
     */
    public function testBelongsTo()
    {
        $member = $this->factory(Member::class)->state(PremiumMember::class)->create();
        $subscription = $this->factory(Subscription::class)->create(['member_id' => $member->id]);

        $this->assertInstanceOf(PremiumMember::class, $subscription->member);
    }

    /**
     * Test that has many return the correct sub types.
     *
     * If this test and the above pass, relationships should work just fine :)
     */
    public function testHasMany()
    {
        $plan = $this->factory(Plan::class)->create();

        $this->factory(Member::class, 3)
            ->state(PremiumMember::class)
            ->create(['plan_id' => $plan->id]);

        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['plan_id' => $plan->id]);

        $this->assertCount(3, $plan->members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $plan->members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }
}
