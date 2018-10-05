<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'vehicle_id', 'customer_id', 'invoice_id', 'created_by', 'date', 'is_invoiced'
    ];

    /**
     * Get the vehicle the word order belongs to
     */
    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle');
    }

    /**
     * Get the customer the word order belongs to
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }
     
    /**
     * Get the jobs associated with this work order
     */
    public function jobs()
    {
        return $this->hasMany('App\Job');
    } 

    /**
     * Get the invoice associated with this work order
     */
    public function invoice()
    {
        return $this->hasOne('App\Invoice');
    }       
}
