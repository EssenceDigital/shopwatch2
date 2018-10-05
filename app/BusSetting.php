<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusSetting extends Model
{

	/** 
	 * The related table
	*/
	protected $table = 'bus_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'shop_rate', 'gst_rate', 'shop_supply_rate', 'city', 'province', 'address', 'postal_code', 'phone'
    ];
}
