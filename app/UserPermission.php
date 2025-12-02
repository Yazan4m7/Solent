<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public function permissons(){
        return $this->belongsTo('App\Permission', 'permission_id', 'id');
    }
}
