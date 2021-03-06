<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first', 'last', 'phone_one', 'phone_two'
    ];

    /**
     * Get the vehicles owned by the customer.
     */
    public function vehicles()
    {
        return $this->hasMany('App\Vehicle');
    }    
}
