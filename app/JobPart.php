<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobPart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'job_id', 'supplier_id', 'part_invoice_number', 'title', 'total_cost', 'billing_price'
    ];

    /**
     * Get the job the part belongs to
     */
    public function job()
    {
        return $this->belongsTo('App\Job');
    }    

    /**
     * Get the supplier the part belongs to
     */
    public function supplier()
    {
        return $this->belongsTo('App\Supplier');
    }      
}
