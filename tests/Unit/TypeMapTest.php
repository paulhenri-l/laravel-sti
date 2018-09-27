<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Relations\Relation;
use PHL\LaravelSTI\TypeMap;
use Tests\Fakes\PremiumMember;
use Tests\Fakes\RegularMember;
use Tests\TestCase;

class TypeMapTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Relation::morphMap([
            'regular_member' => RegularMember::class,
            'premium_member' => PremiumMember::class
        ]);
    }

    /**
     * You can get the class name from an alias.
     */
    public function testGetClassNameFromAlias()
    {
        $this->assertEquals(
            RegularMember::class,
            TypeMap::getClassName('regular_member')
        );

        $this->assertEquals(
            PremiumMember::class,
            TypeMap::getClassName('premium_member')
        );
    }

    /**
     * You can get the alias from a class name.
     */
    public function testGetAliasFromClassName()
    {
        $this->assertEquals(
            'regular_member',
            TypeMap::getAlias(RegularMember::class)
        );

        $this->assertEquals(
            'premium_member',
            TypeMap::getAlias(PremiumMember::class)
        );
    }
}
