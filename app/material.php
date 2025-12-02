<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class material extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'price'];

    public function jobtypes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\materialJobtype', 'material_id', 'id');
    }

}
