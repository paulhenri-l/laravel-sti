<?php

namespace Tests\Unit\Models\Concerns;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Tests\TestCase;

class STISubtypeTest extends TestCase
{
    // Careful - we do not want to overwrite exisiting members of other type when using *OrCreate
    // Add tests for cases where it should not find something find findOrFail etc...

    /** @test */
    public function first_should_return_an_object_of_the_correct_type()
    {
        $this->factory(Member::class)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::first();

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function find_should_return_an_object_of_the_correct_type()
    {
        $createdMemeber = $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::find($createdMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function find_should_not_be_able_to_find_objects_of_other_types()
    {
        $createdMemeber = $this->factory(Member::class)->state(PremiumMember::class)->create();

        $member = RegularMember::find($createdMemeber->id);

        $this->assertNull($member);
    }

    /** @test */
    public function find_or_fail_should_return_an_object_of_the_correct_type()
    {
        $createdMemeber = $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::findOrFail($createdMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function find_or_fail_should_fail_if_it_looks_for_an_object_of_another_type()
    {
        $createdMemeber = $this->factory(Member::class)->state(PremiumMember::class)->create();

        $this->expectException(ModelNotFoundException::class);
        $member = RegularMember::findOrFail($createdMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function first_or_create_should_return_an_object_of_the_correct_type_if_it_finds_it()
    {
        $createdMemeber = $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::firstOrCreate(['id' => $createdMemeber->id], ['name' => 'test']);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function first_or_create_should_return_an_object_of_the_correct_type_when_it_creates_it()
    {
        $member = RegularMember::firstOrCreate(['id' => 1], [
            'type' => RegularMember::class,
            'name' => 'test-created-user',
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('test-created-user', $member->name);
    }

    /** @test */
    public function first_or_new_should_return_an_object_of_the_correct_type_if_it_finds_it()
    {
        $createdMemeber = $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::firstOrNew(['id' => $createdMemeber->id], ['name' => 'test']);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /** @test */
    public function first_or_new_should_return_an_object_of_the_correct_type_when_it_makes_it()
    {
        $member = RegularMember::firstOrNew(['id' => 1], [
            'type' => RegularMember::class,
            'name' => 'test-created-user',
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('test-created-user', $member->name);
    }

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

    /** @test */
    public function take_should_return_objects_of_the_correct_type()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $members = RegularMember::take(3)->get();

        $this->assertCount(0, $members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }

    /** @test */
    public function all_should_return_objects_of_different_types()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $members = RegularMember::all();

        $this->assertCount(0, $members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }

    /** @test */
    public function paginate_should_return_objects_of_different_types()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $members = RegularMember::paginate();

        $this->assertCount(0, $members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }

    /** @test */
    public function each_should_iterate_over_objects_of_the_correct_type()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        RegularMember::each(function ($member) use (&$results) {
            $this->updateMemberCount($member, $results);
        });

        $this->assertEquals(0, $results['premium_count']);
        $this->assertEquals(1, $results['regular_count']);
    }

    /** @test */
    public function chunk_should_iterate_over_objects_of_the_correct_type()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        RegularMember::chunkById(10, function ($members) use (&$results) {
            foreach ($members as $member) {
                $this->updateMemberCount($member, $results);
            }
        });

        $this->assertEquals(0, $results['premium_count']);
        $this->assertEquals(1, $results['regular_count']);
    }

    /** @test */
    public function cursor_should_iterate_over_objects_of_the_correct_type()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        foreach (RegularMember::cursor() as $member) {
            $this->updateMemberCount($member, $results);
        }

        $this->assertEquals(0, $results['premium_count']);
        $this->assertEquals(1, $results['regular_count']);
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
