<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EotRequest extends Model
{
    use SoftDeletes;

    protected $fillable = ['request_number', 'title'];
}
