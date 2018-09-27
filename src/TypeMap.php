<?php

namespace PHL\LaravelSTI;

use Illuminate\Database\Eloquent\Relations\Relation;

class TypeMap
{
    /**
     * Return the class name that is associated with the given alias.
     */
    public static function getClassName($alias)
    {
        return Relation::getMorphedModel($alias);
    }

    /**
     * Return the alias that is associated with the given class name.
     */
    public static function getAlias($searchedClassName)
    {
        $results = array_filter(Relation::$morphMap, function ($className) use ($searchedClassName) {
            return $className == $searchedClassName;
        });

        return key($results);
    }
}
