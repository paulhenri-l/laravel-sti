<?php

namespace Tests\Unit\Models\Concerns;

use Tests\Fakes\Member;
use Tests\Fakes\RegularMember;
use Tests\TestCase;

class STISubtypeTest extends TestCase
{
    /** @test */
    public function update_or_create_should_return_an_object_of_the_correct_type_if_it_updates_it()
    {
        $createdMemeber = $this->factory(Member::class)->state(RegularMember::class)->create(['name' => 'test-name']);

        $member = RegularMember::updateOrCreate(['id' => $createdMemeber->id], ['name' => 'test-updated-name']);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('test-updated-name', $member->name);
    }

    /** @test */
    public function update_or_create_should_return_an_object_of_the_correct_type_when_it_creates_it()
    {
        $member = RegularMember::updateOrCreate(['id' => 1], [
            'type' => RegularMember::class,
            'name' => 'test-created-user',
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('test-created-user', $member->name);
    }
}
