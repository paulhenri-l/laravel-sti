# Laravel STI

> This is my take at bringing Single Table Inheritance (STI) to the Eloquent ORM.

*This package can be used outside of a laravel app*

## Usage

Besides using the trait and adding the type column there is nothing much to do.

```php
class Member extends Illuminate\Database\Eloquent\Model
{
    use PHL\LaravelSTI\STI;
}
```

```php
Capsule::schema()->create('members', function ($table) {
    // ...
    $table->type();
    // ...
});
```

You can now extend the Member model.

```php
class PremiumMember extends Member
{
    //
}

class RegularMember extends Member
{
    //
}
```

And enjoy single table inheritance!

## Configuration

Out of the box there is absolutely nothing to configure. You may want to change
the defaults though.

### Type column

By default the type column is named `type` if you want to use another name you
can specify it in the migration and in the model.

```php
class Member extends Illuminate\Database\Eloquent\Model
{
    use PHL\LaravelSTI\STI;
    
    protected static $stiTypeKey = 'custom_type_column'
}
```

```php
Capsule::schema()->create('members', function ($table) {
    // ...
    $table->type('custom_type_column');
    // ...
});
```

### Type value

If you do not want your type column to contain the class name you can use
Eloquent's `Relation::morphMap()` function to add mapping between a name
and a class.

```php
Relation::morphMap([
    'regular_member' => RegularMember::class,
]);
```

Now the type column will be filled with `regular_member` instead of `Member`.
This helps avoid leaking code details into the DB.

## Read the source Luke!

If you are currious about the implementation details, the code and tests have 
been heavily documented :)