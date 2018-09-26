<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'members';

    protected $guarded = [];
}
