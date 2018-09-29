<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
