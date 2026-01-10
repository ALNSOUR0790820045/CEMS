<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeBarEvent extends Model
{
    use SoftDeletes;

    protected $fillable = ['event_number', 'title'];
}
