<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class lab extends Model
{

    use SoftDeletes;
    protected $guarded = ['id'];


    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public function unitsMilled($from = -2,$to = -2)
    {
       if ($from != -2 && $to != -2)
            $jobs = job::where('milling_lab', $this->id)->whereBetween('created_at', [ $from. ' 00:00', $to . ' 23:59'])->get();
       else
            $jobs = job::where('milling_lab', $this->id)->get();
       $units = 0;
       foreach($jobs as $job)
           $units += count(explode(',',$job->unit_num));
       return $units;
    }
}
