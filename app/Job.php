<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'work_order_id', 'tech', 'title', 'description', 'hours', 'tech_hourly_rate', 'shop_rate', 'is_complete', 'tech_pay_total', 'parts_total_cost', 'parts_total_billed', 'job_labour_total', 'job_grand_total'
    ];

    /**
     * Get the work order the job belongs to
     */
    public function work_order()
    {
        return $this->belongsTo('App\WorkOrder');
    }

    /**
     * Get the parts assigned to this job
     */
    public function parts()
    {
        return $this->hasMany('App\JobPart');
    }     
}
