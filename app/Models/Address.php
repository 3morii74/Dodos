<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'alias', 'details', 'phone', 'city', 'postal_code'];
}
