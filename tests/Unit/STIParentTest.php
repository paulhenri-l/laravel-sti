<?php

namespace Tests\Unit\Models\Concerns;

use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Tests\TestCase;

class STIParentTest extends TestCase
{
    /**
     * Test that first returns objects of the correct type.
     */
    public function testFirst()
    {
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = Member::first();

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /**
     * Test that find returns objects of the correct type.
     */
    public function testFind()
    {
        $createdMemeber = $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create();

        $member = Member::find($createdMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /**
     * Test that find or fail returns objects of the correct type.
     */
    public function testFindOrFail()
    {
        $createdMemeber = $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create();

        $member = Member::findOrFail($createdMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /**
     * Test that firstOrNew returns objects of the correct type when it
     * finds or makes one.
     */
    public function testFirstOrNew()
    {
        // First
        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'find-me']);

        $member = Member::firstOrNew(['name' => 'find-me'], [
            'bio' => 'new-bio'
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertNotEquals('new-bio', $member->bio);
        $this->assertTrue($member->exists);

        // New
        $member = Member::firstOrNew(['name' => 'i-do-not-exists'], [
            'type' => RegularMember::class,
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('i-do-not-exists', $member->name);
        $this->assertFalse($member->exists);
    }

    /**
     * Test that firstOrCreate returns objects of the correct type when it
     * finds or creates one.
     */
    public function testFirstOrCreate()
    {
        // First
        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'find-me']);

        $member = Member::firstOrCreate(['name' => 'find-me'], [
            'type' => RegularMember::class,
            'bio' => 'new-bio'
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertNotEquals('new-bio', $member->bio);
        $this->assertTrue($member->exists);

        // Create
        $member = Member::firstOrCreate(['name' => 'i-do-not-exists'], [
            'type' => RegularMember::class,
            'bio' => 'not-found',
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('not-found', $member->bio);
        $this->assertTrue($member->wasRecentlyCreated);
    }

    /**
     * Test that firstOrNew returns objects of the correct type when it
     * updates or creates one.
     */
    public function testUpdateOrCreate()
    {
        // Update
        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'find-me']);

        $member = Member::updateOrCreate(['name' => 'find-me'], [
            'bio' => 'updated'
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('updated', $member->bio);
        $this->assertFalse($member->wasRecentlyCreated);

        // Create
        $member = Member::updateOrCreate(['name' => 'i-do-not-exists'], [
            'type' => RegularMember::class,
            'bio' => 'created',
        ]);

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('created', $member->bio);
        $this->assertTrue($member->wasRecentlyCreated);
    }

    /**
     * Test that take returns objects of the correct type.
     */
    public function testTake()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $this->assertInstanceOf(
            PremiumMember::class,
            Member::whereType(PremiumMember::class)->take(1)->get()->first()
        );

        $this->assertInstanceOf(
            RegularMember::class,
            Member::whereType(RegularMember::class)->take(1)->get()->first()
        );
    }

    /**
     * Test that all returns objects of the correct type.
     */
    public function testAll()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $members = Member::all();

        $this->assertCount(2, $members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }

    /**
     * Test that paginate returns objects of the correct type.
     */
    public function testPaginate()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 1)->state(RegularMember::class)->create();

        $members = Member::paginate();

        $this->assertCount(2, $members->filter(function ($member) {
            return $member instanceof PremiumMember;
        }));

        $this->assertCount(1, $members->filter(function ($member) {
            return $member instanceof RegularMember;
        }));
    }

    /**
     * Test that each iterates over objects of the correct type.
     */
    public function testEach()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        Member::each(function ($member) use (&$results) {
            $this->updateMemberCount($member, $results);
        });

        $this->assertEquals(2, $results['premium_count']);
        $this->assertEquals(1, $results['regular_count']);
    }

    /**
     * Test that chunk iterates over objects of the correct type.
     */
    public function testChunk()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        Member::chunkById(10, function ($members) use (&$results) {
            foreach ($members as $member) {
                $this->updateMemberCount($member, $results);
            }
        });

        $this->assertEquals(2, $results['premium_count']);
        $this->assertEquals(1, $results['regular_count']);
    }

    /**
     * Test that chunk iterates over objects of the correct type.
     */
    public function testCursor()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $results = ['regular_count' => 0, 'premium_count' => 0];

        foreach (Member::cursor() as $member) {
            $this->updateMemberCount($member, $results);
        }

        $this->assertEquals(2, $results['premium_count']);
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
