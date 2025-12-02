<?php

namespace App;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use JustSteveKing\Laravel\FeatureFlags\Concerns\HasFeatures;
class User extends Authenticatable
{

    use SoftDeletes;
    use HasFeatures;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function permissions(){
        return $this->hasMany('App\UserPermission', 'user_id', 'id');
    }
    //helper
    public function fullName(){
        return $this->first_name .' ' . $this->last_name ;
    }

}
