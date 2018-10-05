<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name'
    ];

    /**
     * Get the parts that belong to this supplier
     */
    public function parts()
    {
        return $this->hasMany('App\JobPart');
    } 
}
