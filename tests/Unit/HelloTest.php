<?php

namespace Tests\Unit;

use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\TestCase;

class HelloTest extends TestCase
{
    /** @test */
    public function hello()
    {
        $model = $this->factory(Member::class)->state(PremiumMember::class)->create();

        $this->assertInstanceOf(Member::class, $model);
    }
}
