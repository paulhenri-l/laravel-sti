<?php

namespace PHL\LaravelSTI;

use Illuminate\Database\Eloquent\Relations\Relation;

class TypeMap
{
    /**
     * Return the class name that is associated with the given alias. If none
     * can be found it's probably because the given alias is actually the
     * class name.
     */
    public static function getClassName($alias)
    {
        return Relation::getMorphedModel($alias) ?? $alias;
    }

    /**
     * Return the alias that is associated with the given class name. If none
     * can be found than return the given classname and it will be used
     * as the alias.
     */
    public static function getAlias($searchedClassName)
    {
        foreach (Relation::$morphMap as $key => $className) {
            if ($className != $searchedClassName) {
                continue;
            }

            return $key;
        }

        return $searchedClassName;
    }
}
