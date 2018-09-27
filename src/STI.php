<?php

namespace PHL\LaravelSTI;

trait STI
{
    // Use initialiazable traits to dynamically add the type property

    /**
     * Scope all queries to the current subtype if it is made from a subtype.
     */
    public static function bootSTI()
    {
        if (static::inSTIParent()) {
            return;
        }

        static::addGlobalScope(function ($query) {
            return $query->where(static::typeKey(), static::class);
        });
    }

    /**
     * Allways use the STI parent model's table.
     */
    public function getTable()
    {
        if (static::inSTIParent()) {
            return parent::getTable();
        }

        return $this->newSTIParent()->getTable();
    }

    /**
     * Return a new instance of the STI model or the subtype it represents.
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $type = $this->findClassnameThanksToAttributes($attributes);

        $model = new $type($attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
    }

    /**
     * Return a new instance of the STI model or the subtype it represents.
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array)$attributes;
        $type = $this->findClassnameThanksToAttributes($attributes);

        $model = (new $type)->newInstance([], true);

        $model->setRawAttributes($attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * Here we'll let laravel do the heavy lifting in its original updateOrCrate
     * function. Once that's done we'll check if the returned model has been
     * recently created. When that's the case we simply have to call fresh on
     * it to get it "downcasted" into the correct type.
     *
     * As this issue only occurs when calling updateOrCreate on the parent model
     * (the one that uses the STI trait) and not the subtype ones we'll exit
     * early if we can see that we are in the context of a subtype.
     */
    public function overloadedUpdateOrCreate(...$args)
    {
        $model = $this->forwardCallTo($this->newQuery(), 'updateOrCreate', $args);

        if (!static::inSTIParent()) {
            return $model;
        }

        if ($model->wasRecentlyCreated) {
            $model = $model->fresh();
            $model->wasRecentlyCreated = true;
        }

        return $model;
    }

    /**
     * Update or create is not able to return models in the correct subtype when
     * it creates them. So we'll have to overload the original method in order
     * to add support for this feature.
     *
     * As updateOrCreate lives in the builder it is not directly available on
     * the model. It also means that this function can be called both on an
     * instance or statically thanks to the model's __call() and __callStatic()
     * magic methods.
     *
     * Our new code for the updateOrCreate method can only work in an instance
     * context. So we'll have to be creative in order to keep the abbility to
     * call it both statically and from an instance. That is the case because
     * adding it as an instance method will make it unavailable for static calls.
     *
     * The workaround for this issue is to add our implementation in a new
     * instance method named overloadedUpdateOrCreate() that will get called
     * from this updateOrCreate method.
     *
     * This updateOrCreate method being static means that it can be called
     * from both a static and an instance context.
     *
     * @param array $attributes
     * @param array $values
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public static function updateOrCreate(...$args)
    {
        return (new static())->overloadedUpdateOrCreate(...$args);
    }

    /**
     * Are we currently in a subtype or the parent model.
     *
     * Even though this if is extremely weird, it works and is covered by
     * the testsuite :)
     * http://php.net/manual/en/language.oop5.late-static-bindings.php
     */
    public static function inSTIParent()
    {
        return static::class === static::getSTIParentClassname();
    }

    /**
     * Return the classname of the parent STI model.
     */
    public static function getSTIParentClassname()
    {
        return self::class;
    }

    /**
     * Return a new instance of the STI parent model.
     */
    public function newSTIParent()
    {
        $class = $this->getSTIParentClassname();
        return new $class;
    }

    /**
     * Given an array of attributes that may or may not contain a type what
     * type of object should we create?
     */
    protected function findClassnameThanksToAttributes(array $attributes)
    {
        return $attributes[$this->typeKey()] ?? static::class;
    }

    /**
     * Return the column in which we should look for the model's type.
     */
    protected static function typeKey()
    {
        return static::$stiTypeKey ?? 'type';
    }
}
