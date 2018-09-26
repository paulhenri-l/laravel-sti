<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;
use PHL\LaravelSTI\STI;

class Member extends Model
{
    use STI;

    protected $guarded = [];
}
