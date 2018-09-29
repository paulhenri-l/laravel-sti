<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Relations\Relation;
use PHL\LaravelSTI\TypeMap;
use Tests\TestCase;

class TypeMapTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Relation::morphMap([
            'foo' => Foo::class,
            'bar' => Bar::class
        ]);
    }

    /**
     * You can get the class name from an alias.
     */
    public function testGetClassNameFromAlias()
    {
        $this->assertEquals(
            Foo::class,
            TypeMap::getClassName('foo')
        );

        $this->assertEquals(
            Bar::class,
            TypeMap::getClassName('bar')
        );
    }

    /**
     * You can get the alias from a class name.
     */
    public function testGetAliasFromClassName()
    {
        $this->assertEquals(
            'foo',
            TypeMap::getAlias(Foo::class)
        );

        $this->assertEquals(
            'bar',
            TypeMap::getAlias(Bar::class)
        );
    }
}
