<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'customer_id', 'year', 'make', 'model', 'vin', 'plate_number'
    ];

    /**
     * Get the customer that owns the vehicle
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }    

    /**
     * Get the work orders belonging to the vehicle
     */
    public function work_orders()
    {
        return $this->hasMany('App\WorkOrder');
    } 

    /**
     * Get the invoices belonging to the vehicle
     */
    public function invoices()
    {
        return $this->hasMany('App\Invoice');
    }         
}
