<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use PHL\LaravelSTI\STI;

class Member extends Model
{
    use STI;

    protected $guarded = [];

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
