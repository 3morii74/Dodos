<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'profile_img', 'role', 'active', 'slug',
    ];

    protected $hidden = [
        'password', 'password_reset_code',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    //public function wishlist()
    //{
    //    return $this->belongsToMany(Product::class, 'wishlist');
    //}

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}