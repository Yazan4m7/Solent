<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class caseTag extends Model{
    use SoftDeletes;
protected $fillable = ['case_id','tag_id','added_by'];
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }
    public function case(){
        return $this->belongsTo('App\sCase', 'case_id', 'id');
    }

    public function originalTagRecord(){
        return $this->belongsTo('App\tag', 'tag_id', 'id');
    }
}
