<?php

namespace Tests\Unit\Models\Concerns;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Tests\Fakes\Member;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Tests\TestCase;

class STISubtypeTest extends TestCase
{
    /**
     * Test that create creates objects of the correct subtype.
     */
    public function testCreate()
    {
        $member = PremiumMember::create(['name' => 'name']);

        $this->assertInstanceOf(PremiumMember::class, $member);
        $this->assertEquals(PremiumMember::class, $member->type);
    }

    /**
     * Test that save saves objects of the correct subtype.
     */
    public function testSave()
    {
        $member = tap(new RegularMember(['name' => 'name']))->save();

        $this->assertInstanceOf(RegularMember::class, $member);
        $this->assertEquals('regular_member', $member->type);
    }

    /**
     * Test that count is scoped to the subtype it is called on.
     */
    public function testCount()
    {
        $this->factory(Member::class, 2)->state(PremiumMember::class)->create();
        $this->factory(Member::class, 3)->state(RegularMember::class)->create();

        $this->assertEquals(2, PremiumMember::count());
        $this->assertEquals(3, RegularMember::count());
    }

    /**
     * Test that first returns only objects of the subtype that it is called on.
     */
    public function testFirst()
    {
        $this->factory(Member::class)->state(PremiumMember::class)->create();
        $this->factory(Member::class)->state(RegularMember::class)->create();

        $member = RegularMember::first();

        $this->assertInstanceOf(RegularMember::class, $member);
    }

    /**
     * Test that find returns only objects of the subtype that it is called on.
     */
    public function testFind()
    {
        $createdRegularMemeber = $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create();

        $createdPremiumMemeber = $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create();

        $regularMember = RegularMember::find($createdRegularMemeber->id);
        $premiumMember = RegularMember::find($createdPremiumMemeber->id);

        $this->assertInstanceOf(RegularMember::class, $regularMember);
        $this->assertNull($premiumMember);
    }

    /**
     * Test that find returns only objects of the subtype that it is called on.
     */
    public function testFindOrFail()
    {
        $createdRegularMemeber = $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create();

        $createdPremiumMemeber = $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create();

        $premiumMember = PremiumMember::findOrFail($createdPremiumMemeber->id);
        $this->assertInstanceOf(PremiumMember::class, $premiumMember);

        $this->expectException(ModelNotFoundException::class);
        PremiumMember::findOrFail($createdRegularMemeber->id);
    }

    /**
     * Test that firstOrNew returns only objects of the subtype that it is
     * called on. As a side effect this also tests that it makes them when not
     * found.
     */
    public function testFirstOrNew()
    {
        $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create(['name' => 'premium-find-me']);

        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'not-premium-find-me']);

        // First
        $premiumMember = PremiumMember::firstOrNew(
            ['name' => 'premium-find-me'], ['bio' => 'not-found']
        );
        $this->assertInstanceOf(PremiumMember::class, $premiumMember);
        $this->assertNotEquals('not-found', $premiumMember->bio);
        $this->assertTrue($premiumMember->exists);

        // New
        $notRegularMember = PremiumMember::firstOrNew(
            ['name' => 'not-premium-find-me'], ['bio' => 'not-found']
        );
        $this->assertInstanceOf(PremiumMember::class, $notRegularMember);
        $this->assertEquals('not-found', $notRegularMember->bio);
        $this->assertFalse($notRegularMember->exists);
    }

    /**
     * Test that firstOrCreate returns only objects of the subtype that it is
     * called on. As a side effect this also tests that it creates them when not
     * found.
     */
    public function testFirstOrCreate()
    {
        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'regular-find-me']);

        $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create(['name' => 'not-regular-find-me']);

        // First
        $regularMember = RegularMember::firstOrCreate(
            ['name' => 'regular-find-me'], ['bio' => 'not-found']
        );
        $this->assertInstanceOf(RegularMember::class, $regularMember);
        $this->assertNotEquals('not-found', $regularMember->bio);
        $this->assertTrue($regularMember->exists);

        // Create
        $notRegularMember = RegularMember::firstOrCreate(
            ['name' => 'not-regular-find-me'], ['bio' => 'not-found']
        );
        $this->assertInstanceOf(RegularMember::class, $notRegularMember);
        $this->assertEquals('not-found', $notRegularMember->bio);
        $this->assertTrue($notRegularMember->exists);
        $this->assertTrue($notRegularMember->wasRecentlyCreated);
    }

    /**
     * Tests that when you call *OrCreate and use the id of an object of another
     * subtype in the search attributes that you will get an exception.
     *
     * This is due to the fact that a subtype cannot see other subtypes, it will
     * therefore attempt to create a new model if the search attributes contains
     * the id of another subtype. As ids should be unique the db will fail.
     *
     * IMO this is more of a limitation of Single table inheritance than a bug
     * that's why I will not attempt to handle this situation.
     */
    public function testfirstOrCreateAndUpdateOrCreateFailsWithIds()
    {
        $createdPremiumMember = $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create();

        $this->expectException(QueryException::class);
        RegularMember::firstOrCreate(['id' => $createdPremiumMember->id], [
            'name' => 'test',
        ]);

        $this->expectException(QueryException::class);
        RegularMember::updateOrCreate(['id' => $createdPremiumMember->id], [
            'name' => 'test',
        ]);
    }

    /**
     * Test that firstOrNew returns objects of the correct type when it
     * updates or creates one.
     */
    public function testUpdateOrCreate()
    {
        $this->factory(Member::class)
            ->state(RegularMember::class)
            ->create(['name' => 'regular-find-me']);

        $this->factory(Member::class)
            ->state(PremiumMember::class)
            ->create(['name' => 'not-regular-find-me']);

        // First
        $regularMember = RegularMember::updateOrCreate(
            ['name' => 'regular-find-me'], ['bio' => 'updated']
        );
        $this->assertInstanceOf(RegularMember::class, $regularMember);
        $this->assertEquals('updated', $regularMember->bio);
        $this->assertFalse($regularMember->wasRecentlyCreated);

        // Create
        $notRegularMember = RegularMember::updateOrCreate(
            ['name' => 'not-regular-find-me'], ['bio' => 'created']
        );
        $this->assertInstanceOf(RegularMember::class, $notRegularMember);
        $this->assertEquals('created', $notRegularMember->bio);
        $this->assertTrue($notRegularMember->wasRecentlyCreated);
    }

    /**
     * Test that take returns only objects of the subtype it is called on.
     */
    public function testTake()
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

    /**
     * Test that all returns only objects of the subtype it is called on.
     */
    public function testAll()
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

    /**
     * Test that paginate returns only objects of the subtype it is called on.
     */
    public function testPaginate()
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

    /**
     * Test that each iterates only over objects of the subtype it is called on.
     */
    public function testEach()
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

    /**
     * Test that chunk iterates only over objects of the subtype it is
     * called on.
     */
    public function testChunk()
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

    /**
     * Test that cursor iterates only over objects of the subtype it is
     * called on.
     */
    public function testCursor()
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
}
