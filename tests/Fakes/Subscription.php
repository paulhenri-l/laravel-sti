<?php

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
