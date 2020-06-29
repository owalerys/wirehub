<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teamable extends Model
{
    use SoftDeletes;
}
